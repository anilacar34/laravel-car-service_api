<?php
namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

trait SoftDeleteCustom {
    use SoftDeletes;

    public function runSoftDelete() {
        $query = $this->newQueryWithoutScopes()->where($this->getKeyName(), $this->getKey());

        $query->update([
            'deleted_at'  => Carbon::now(),
            'deleted_by'  => auth()->user()->id
        ]);
    }

    public function restore() {
        $this->deleted_at = NULL;
        $this->deleted_by = 0;
        $this->saveQuietly();
        return true;
    }
}
