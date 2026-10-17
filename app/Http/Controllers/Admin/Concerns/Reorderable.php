<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Drag-and-drop ordering for any model with a `sort_order` column.
 */
trait Reorderable
{
    abstract protected function reorderModel(): string;

    public function reorder(Request $request): RedirectResponse
    {
        $model = $this->reorderModel();

        $ids = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
        ])['ids'];

        DB::transaction(function () use ($ids, $model) {
            foreach ($ids as $position => $id) {
                $model::whereKey($id)->update(['sort_order' => $position + 1]);
            }
        });

        return back()->with('success', 'Order updated.');
    }
}
