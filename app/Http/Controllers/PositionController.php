<?php

namespace App\Http\Controllers;

use App\Http\Requests\PositionRequest;
use App\Models\Position;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::latest()->paginate(15);

        return view('admin.positions.index', compact('positions'));
    }

    public function create()
    {
        return view('admin.positions.create');
    }

    public function store(PositionRequest $request)
    {
        Position::create($request->validated());

        return redirect()->route('admin.positions.index')
            ->with('status', 'Jabatan berhasil dibuat.');
    }

    public function show(Position $position)
    {
        return redirect()->route('admin.positions.edit', $position);
    }

    public function edit(Position $position)
    {
        return view('admin.positions.edit', compact('position'));
    }

    public function update(PositionRequest $request, Position $position)
    {
        $position->update($request->validated());

        return redirect()->route('admin.positions.index')
            ->with('status', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(Position $position)
    {
        $position->delete();

        return redirect()->route('admin.positions.index')
            ->with('status', 'Jabatan berhasil dihapus.');
    }
}
