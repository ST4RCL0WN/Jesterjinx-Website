<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Auth;

use App\Models\SitePage;
use App\Services\PageService;

use App\Http\Controllers\Controller;

class PageController extends Controller
{
    /**
     * Shows the page index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('admin.pages.pages', [
            'pages' => SitePage::orderBy('title')->paginate(20)
        ]);
    }
    
    /**
     * Shows the create page page. 
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreatePage()
    {
        return view('admin.pages.create_edit_page', [
            'page' => new SitePage
        ]);
    }
    
    /**
     * Shows the edit page page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditPage($id)
    {
        $page = SitePage::find($id);
        if(!$page) abort(404);
        return view('admin.pages.create_edit_page', [
            'page' => $page
        ]);
    }

    /**
     * Creates or edits a page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\PageService  $service
     * @param  int|null                  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditPage(Request $request, PageService $service, $id = null)
    {
        $id ? $request->validate(SitePage::$updateRules) : $request->validate(SitePage::$createRules);
        $data = $request->only([
            'key', 'title', 'text', 'is_visible', 'can_comment'
        ]);
        if($id && $service->updatePage(SitePage::find($id), $data, Auth::user())) {
            flash('Page updated successfully.')->success();
        }
        else if (!$id && $page = $service->createPage($data, Auth::user())) {
            flash('Page created successfully.')->success();
            return redirect()->to('admin/pages/edit/'.$page->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Gets the page deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeletePage($id)
    {
        $page = SitePage::find($id);
        return view('admin.pages._delete_page', [
            'page' => $page,
        ]);
    }

    /**
     * Deletes a page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\PageService  $service
     * @param  int                       $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeletePage(Request $request, PageService $service, $id)
    {
        if($id && $service->deletePage(SitePage::find($id))) {
            flash('Page deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/pages');
    }
}
