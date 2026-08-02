<?php

namespace App\Support;

use App\Models\Department;
use App\Models\Position;

class OrganizationLookup
{
    public static function department(string $name, ?int $companyId = null): Department
    {
        $name = self::cleanName($name, 'General');
        $normalized = self::normalizeName($name);

        $department = Department::query()
            ->when($companyId, fn ($query) => $query->where('company_id', $companyId))
            ->whereRaw('LOWER(TRIM(name)) = ?', [$normalized])
            ->first();

        return $department ?: Department::create([
            'company_id' => $companyId,
            'name' => $name,
        ]);
    }

    public static function position(string $name, ?int $companyId = null): Position
    {
        $name = self::cleanName($name, 'Staff');
        $normalized = self::normalizeName($name);

        $position = Position::query()
            ->when($companyId, fn ($query) => $query->where('company_id', $companyId))
            ->whereRaw('LOWER(TRIM(name)) = ?', [$normalized])
            ->first();

        return $position ?: Position::create([
            'company_id' => $companyId,
            'name' => $name,
        ]);
    }

    public static function cleanName(string $name, string $fallback): string
    {
        $name = str_replace("\xc2\xa0", ' ', $name);
        $name = preg_replace('/\s+/', ' ', trim($name));

        return $name !== '' ? $name : $fallback;
    }

    public static function normalizeName(string $name): string
    {
        return strtolower(self::cleanName($name, ''));
    }
}
