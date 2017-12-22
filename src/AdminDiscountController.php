<?php

namespace Larrock\ComponentDiscount;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Lang;
use Larrock\ComponentDiscount\Facades\LarrockDiscount;
use Larrock\ComponentDiscount\Models\Discount;
use Larrock\Core\Component;
use Larrock\Core\Traits\AdminMethodsDestroy;
use Larrock\Core\Traits\AdminMethodsEdit;
use Larrock\Core\Traits\ShareMethods;
use Redirect;
use Session;
use Validator;
use View;

/**
 * Class AdminDiscountController
 * @package Larrock\ComponentDiscount
 */

class AdminDiscountController extends Controller
{
    use AdminMethodsEdit, AdminMethodsDestroy, ShareMethods;

    protected $config;

    public function __construct()
    {
        $this->shareMethods();
        $this->config = LarrockDiscount::shareConfig();

        \Config::set('breadcrumbs.view', 'larrock::admin.breadcrumb.breadcrumb');
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
            return redirect()->to('/admin/'. LarrockDiscount::getName() .'/'. $search_blank->id. '/edit');
        }

        $validator = Validator::make($request->all(), LarrockDiscount::getValid());
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
            return Redirect::to('/admin/'. LarrockDiscount::getName() .'/'. $data->id .'/edit')->withInput();
        }
        Session::push('message.danger',  Lang::get('apps.create.error'));
        return back()->withInput();
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
}