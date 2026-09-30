<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Services\BannerLocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('location')->orderBy('position')->get();
        $bannerLocations = BannerLocationService::keys();

        return view('admin.banners.index', compact('banners', 'bannerLocations'));
    }

    public function create()
    {
        $bannerLocations = BannerLocationService::keys();

        return view('admin.banners.create', compact('bannerLocations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link' => 'nullable|url',
            'location' => ['required', 'string', BannerLocationService::validationRule()],
            'position' => 'integer',
            'active' => 'boolean'
        ]);

        $data = $request->except('image');
        $data['position'] = $request->integer('position', 0);
        
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/banners'), $filename);
            $data['image_path'] = 'images/banners/' . $filename;
        }

        $data['active'] = $request->has('active');

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner creado exitosamente.');
    }

    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        $bannerLocations = BannerLocationService::keys();

        return view('admin.banners.edit', compact('banner', 'bannerLocations'));
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link' => 'nullable|url',
            'location' => ['required', 'string', BannerLocationService::validationRule()],
            'position' => 'integer',
            'active' => 'boolean'
        ]);

        $data = $request->except('image');
        $data['position'] = $request->integer('position', 0);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($banner->image_path && file_exists(public_path($banner->image_path))) {
                unlink(public_path($banner->image_path));
            }

            $file = $request->file('image');
            $filename = time() . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/banners'), $filename);
            $data['image_path'] = 'images/banners/' . $filename;
        }

        $data['active'] = $request->has('active');

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        
        if ($banner->image_path && file_exists(public_path($banner->image_path))) {
            unlink(public_path($banner->image_path));
        }
        
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Banner eliminado exitosamente.');
    }
}
