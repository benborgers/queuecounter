<div>
    {{ App\Models\Snapshot::latest()->first()->updated_at->diffForHumans() }}
</div>
