<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Transformers\TagTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TagController extends Controller
{

    /**
     * Get all tags within group
     */
    public function getByGroup($group)
    {
        return $this->successRs($this->collection(Tag::whereGroup($group)->orderBy('id')->get(), new TagTransformer()));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group' => 'required',
            'tag' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        $model = Tag::create([
            'tag' => $request->tag,
            'group' => $request->group,
            'slug' => Str::slug($request->tag),
        ]);

        return $this->successRs(
            fractal()->item($model)->transformWith(new TagTransformer())->toArray()
        );
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'group' => 'required',
            'tag' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        $model = Tag::findOrFail($id);
        $model->update([
            'tag' => $request->tag,
            'group' => $request->group,
            'slug' => Str::slug($request->tag),
        ]);

        return $this->successRs(
            fractal()->item($model)->transformWith(new TagTransformer())->toArray()
        );
    }

    public function destroy($id)
    {
        Tag::findOrFail($id)->delete();
        return $this->successRs(null);
    }
}
