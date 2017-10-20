<?php

namespace Larrock\ComponentDiscount;

use Breadcrumbs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JsValidator;
use Lang;
use Larrock\ComponentDiscount\Models\Discount;
use Larrock\Core\Component;
use Redirect;
use Session;
use Validator;
use View;

class AdminDiscountController extends Controller
{
    protected $config;

    public function __construct()
    {
        $Component = new DiscountComponent();
        $this->config = $Component->shareConfig();

        \Config::set('breadcrumbs.view', 'larrock::admin.breadcrumb.breadcrumb');
        Breadcrumbs::register('admin.'. $this->config->name .'.index', function($breadcrumbs){
            $breadcrumbs->push($this->config->title, '/admin/'. $this->config->name);
        });
    }

    public function index()
    {
        $data['data'] = Discount::with(['get_category_discount'])->get();
        View::share('validator', '');
        return view('larrock::admin.discount.index', $data);
    }

    public function create()
    {
        $test = Request::create('/admin/discount', 'POST', [
            'title' => 'Новая скидка',
            'type' => 'default',
            'date_start' => Carbon::now()->format('Y-m-d H:s:i'),
            'date_end' => Carbon::now()->format('Y-m-d H:s:i'),
            'url' => str_slug('novyy-material'),
            'active' => 0
        ]);
        return $this->store($test);
    }

    public function store(Request $request)
    {
        if($search_blank = Discount::whereUrl('novyy-material')->first()){
            Session::push('message.danger', 'Измените URL этого материала, чтобы получить возможность создать новый');
            return redirect()->to('/admin/'. $this->config->name .'/'. $search_blank->id. '/edit');
        }

        $validator = Validator::make($request->all(), $this->config->valid);
        if($validator->fails()){
            return back()->withInput($request->except('password'))->withErrors($validator);
        }

        $data = new Discount();
        $data->fill($request->all());
        $data->active = $request->input('active', 0);
        $data->position = $request->input('position', 0);
        $data->url = str_slug($request->input('title'));

        if($data->save()){
            \Cache::flush();
            Session::push('message.success', Lang::get('apps.create.success-temp'));
            return Redirect::to('/admin/'. $this->config->name .'/'. $data->id .'/edit')->withInput();
        }
        Session::push('message.danger',  Lang::get('apps.create.error'));
        return back()->withInput();
    }

    public function edit($id)
    {
        $data['data'] = Discount::findOrFail($id);
        $data['app'] = $this->config->tabbable($data['data']);

        $validator = JsValidator::make(Component::_valid_construct($this->config, 'update', $id));
        View::share('validator', $validator);

        Breadcrumbs::register('admin.'. $this->config->name .'.edit', function($breadcrumbs, $data)
        {
            $breadcrumbs->parent('admin.'. $this->config->name .'.index');
            $breadcrumbs->push($data->title);
        });

        return view('larrock::admin.admin-builder.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), Component::_valid_construct($this->config, 'update', $id));
        if($validator->fails()){
            return back()->withInput($request->except('password'))->withErrors($validator);
        }

        $data = Discount::find($id);
        $update_data = $request->all();
        $update_data['url'] = str_slug($request->get('title'));
        if($data->fill($update_data)->save()){
            \Cache::flush();
            Session::push('message.success', Lang::get('apps.update.success', ['name' => $request->input('title')]));
            return back();
        }
        Session::push('message.danger', Lang::get('apps.update.nothing', ['name' => $request->input('title')]));
        return back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if($data = Discount::find($id)){
            $name = $data->title;

            $Component = new DiscountComponent();
            $Component->removeDataPlugins($this->config);

            if($data->delete()){
                \Cache::flush();
                Session::push('message.success', Lang::get('apps.delete.success', ['name' => $name]));
            }else{
                Session::push('message.danger', Lang::get('apps.delete.error', ['name' => $name]));
            }
        }else{
            Session::push('message.danger', 'Такого материала больше нет');
        }

        if($request->get('place') === 'material'){
            return Redirect::to('/admin/'. $this->config->name);
        }
        return back();
    }
}