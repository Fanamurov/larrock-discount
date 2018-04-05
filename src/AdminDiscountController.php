<?php

namespace Larrock\ComponentDiscount;

use Lang;
use View;
use Session;
use Validator;
use Carbon\Carbon;
use LarrockDiscount;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Larrock\Core\Traits\ShareMethods;
use Larrock\Core\Traits\AdminMethodsEdit;
use Larrock\Core\Events\ComponentItemStored;
use Larrock\Core\Traits\AdminMethodsDestroy;
use Larrock\Core\Events\ComponentItemUpdated;
use Larrock\ComponentDiscount\Models\Discount;

/**
 * Class AdminDiscountController.
 */
class AdminDiscountController extends Controller
{
    use AdminMethodsEdit, AdminMethodsDestroy, ShareMethods;

    public function __construct()
    {
        $this->shareMethods();
        $this->config = LarrockDiscount::shareConfig();

        \Config::set('breadcrumbs.view', 'larrock::admin.breadcrumb.breadcrumb');
    }

    /**
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $data['data'] = Discount::with(['getCategoryDiscount'])->get();
        View::share('validator', '');

        return view('larrock::admin.discount.index', $data);
    }

    /**
     * @return $this
     */
    public function create()
    {
        $test = Request::create('/admin/discount', 'POST', [
            'title' => 'Новая скидка',
            'type' => 'default',
            'date_start' => Carbon::now()->format('Y-m-d H:s:i'),
            'date_end' => Carbon::now()->format('Y-m-d H:s:i'),
            'active' => 0,
        ]);

        return $this->store($test);
    }

    /**
     * @param Request $request
     * @return $this
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public function store(Request $request)
    {
        $data = new Discount();
        $data->fill($request->all());
        $data->active = $request->input('active', 0);
        $data->position = $request->input('position', 0);

        $validator = Validator::make($data->toArray(), $this->config->getValid());
        if ($validator->fails()) {
            return back()->withInput($request->except('password'))->withErrors($validator);
        }

        if ($data->save()) {
            event(new ComponentItemStored($this->config, $data, $request));
            \Cache::flush();
            Session::push('message.success', Lang::get('larrock::apps.create.success-temp'));
        } else {
            Session::push('message.danger', Lang::get('larrock::apps.create.error'));
        }

        return redirect()->to('admin/discount/'.$data->id.'/edit');
    }

    /**
     * @param Request $request
     * @param $id
     * @return $this|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public function update(Request $request, $id)
    {
        $data = Discount::find($id);
        $update_data = $request->all();
        $update_data['url'] = str_slug($request->get('title'));
        $data->fill($update_data);

        $validator = Validator::make($data->toArray(), $this->config->getValid($id));
        if ($validator->fails()) {
            return back()->withInput($request->except('password'))->withErrors($validator);
        }

        if ($data->save()) {
            event(new ComponentItemUpdated($this->config, $data, $request));
            \Cache::flush();
            Session::push('message.success', Lang::get('larrock::apps.update.success', ['name' => $request->input('title')]));

            return back();
        }
        Session::push('message.danger', Lang::get('larrock::apps.update.nothing', ['name' => $request->input('title')]));

        return back()->withInput();
    }
}
