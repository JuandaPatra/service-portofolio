<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resep;

class resepController extends Controller
{
    public function index(Request $request)
    {
        // ambil query params
        $search     = $request->query('search');
        $limit      = $request->query('limit', 10); // default 10
        $sort       = $request->query('sort', 'desc'); // asc/desc
        $categoryId = $request->query('category'); // optional

        $reseps = Resep::query();

        // 🔍 SEARCH (judul / deskripsi)
        if ($search) {
            $reseps->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 📂 FILTER BY CATEGORY
        if ($categoryId) {
            $reseps->where('category_id', $categoryId);
        }

        // ↕ SORTING berdasarkan created_at
        if (in_array($sort, ['asc', 'desc'])) {
            $reseps->orderBy('created_at', $sort);
        }

        // 📄 PAGINATION + LIMIT
        $result = $reseps->paginate($limit);

        return ApiFormatter::createApi(200, 'success', $result);
    }


    public function show($slug)
    {
        $resep = Resep::where('slug', $slug)->firstOrFail();

        if ($resep) {
            return ApiFormatter::createApi(200, 'success', $resep);
        } else {
            return ApiFormatter::createApi(404, 'Resep not found');
        }
    }

    public function addResep(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'source' => 'required|string',
            'user_id' => 'nullable|integer',
            'category_id' => 'nullable|integer',
            'ingredients' => 'nullable|string',
        ]);

        $imagePath = $request->file('image')->store('images', 'public');

        $resep = Resep::create([
            'title' => $request->name,
            'slug' => \Str::slug($request->name) . '-' . uniqid(),
            'description' => $request->description,
            'image' => $imagePath,
            'source' => $request->source,
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'ingredients' => $request->ingredients,
        ]);

        return ApiFormatter::createApi(201, 'Resep created successfully', $resep);
    }

    public function editResep(Request $request, $slug)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'source' => 'required|string',
            'user_id' => 'nullable|integer',
            'category_id' => 'nullable|integer',
            'ingredients' => 'nullable|string',
        ]);

        $resep = Resep::where('slug', $slug)->firstOrFail();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('resep', 'public');
        } else {
            $imagePath = $resep->image;
        }

        $resep->update([
            'title' => $request->name,
            // 'slug' => \Str::slug($request->name) . '-' . uniqid(),
            'description' => $request->description,
            'image' => $imagePath,
            'source' => $request->source,
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'ingredients' => $request->ingredients,
        ]);

        return ApiFormatter::createApi(200, 'Resep updated successfully', $resep);
    }

    public function deleteResep($slug)
    {
        $resep = Resep::where('slug', $slug)->firstOrFail();
        $resep->delete();

        return ApiFormatter::createApi(200, 'Resep deleted successfully');
    }
}
