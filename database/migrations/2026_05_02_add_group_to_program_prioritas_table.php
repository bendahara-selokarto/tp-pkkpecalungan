<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_prioritas', function (Blueprint $table): void {
            $table->enum('group', [
                'sekretaris-tpk',
                'bendahara-tpk',
                'pokja-i',
                'pokja-ii',
                'pokja-iii',
                'pokja-iv',
            ])
                ->default('sekretaris-tpk')
                ->after('level')
                ->comment('Jabatan/group pemilik buku program kerja');

            $table->index(['group', 'level', 'area_id'], 'program_prioritas_group_level_area_id_index');
        });

        $this->backfillGroupFromCreatorRole();
    }

    public function down(): void
    {
        Schema::table('program_prioritas', function (Blueprint $table): void {
            $table->dropIndex('program_prioritas_group_level_area_id_index');
            $table->dropColumn('group');
        });
    }

    private function backfillGroupFromCreatorRole(): void
    {
        $roleToGroup = [
            'desa-sekretaris' => 'sekretaris-tpk',
            'kecamatan-sekretaris' => 'sekretaris-tpk',
            'desa-bendahara' => 'bendahara-tpk',
            'kecamatan-bendahara' => 'bendahara-tpk',
            'desa-pokja-i' => 'pokja-i',
            'desa-pokja-ii' => 'pokja-ii',
            'desa-pokja-iii' => 'pokja-iii',
            'desa-pokja-iv' => 'pokja-iv',
            'kecamatan-pokja-i' => 'pokja-i',
            'kecamatan-pokja-ii' => 'pokja-ii',
            'kecamatan-pokja-iii' => 'pokja-iii',
            'kecamatan-pokja-iv' => 'pokja-iv',
        ];

        foreach ($roleToGroup as $roleName => $group) {
            $creatorIds = DB::table('model_has_roles')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->where('roles.name', $roleName)
                ->pluck('model_has_roles.model_id');

            foreach ($creatorIds->chunk(500) as $chunk) {
                DB::table('program_prioritas')
                    ->whereIn('created_by', $chunk->all())
                    ->update(['group' => $group]);
            }
        }
    }
};
