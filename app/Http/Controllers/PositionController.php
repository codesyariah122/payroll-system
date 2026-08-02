<?php

namespace App\Http\Controllers;

use App\Http\Requests\PositionRequest;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $positions = Position::where('company_id', $request->user()->company_id)
            ->latest()
            ->paginate(15);

        return view('admin.positions.index', compact('positions'));
    }

    public function create()
    {
        return view('admin.positions.create');
    }

    public function store(PositionRequest $request)
    {
        Position::create($request->validated() + [
            'company_id' => $request->user()->company_id,
        ]);

        return redirect()->route('admin.positions.index')
            ->with('status', 'Jabatan berhasil dibuat.');
    }

    public function show(Position $position)
    {
        return redirect()->route('admin.positions.edit', $position);
    }

    public function edit(Request $request, Position $position)
    {
        $this->authorizeCompany($request, $position);

        return view('admin.positions.edit', compact('position'));
    }

    public function update(PositionRequest $request, Position $position)
    {
        $this->authorizeCompany($request, $position);

        $position->update($request->validated());

        return redirect()->route('admin.positions.index')
            ->with('status', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(Request $request, Position $position)
    {
        $this->authorizeCompany($request, $position);

        $position->delete();

        return redirect()->route('admin.positions.index')
            ->with('status', 'Jabatan berhasil dihapus.');
    }

    private function authorizeCompany(Request $request, Position $position): void
    {
        abort_if($position->company_id !== $request->user()->company_id, 404);
    }
}
