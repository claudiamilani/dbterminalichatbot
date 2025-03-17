<?php

namespace App\DBT;

use App\DBT\Models\TransposeConfig;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DwhOperations
{

    const TYPE_MARCA = 'DWH_MARCA';
    const TYPE_TERMINAL = 'DWH_TERMINALE';
    const TYPE_TAC = 'DWH_TAC';
    const TYPE_ATTRIBUTI = 'DWH_ATTRIBUTI';
    const TYPE_TRASPOSTA = 'DWH_TRASPOSTA';

    private $trasposta_table_name = 'trasposta';


    /**
     *  Return array with all views translated name and check for DB presence
     *
     * @return array[]
     */
    public static function getType(): array
    {
        $constants = [

            self::TYPE_MARCA => [
                'title' => trans('DBT/dwh_operations.types.dwh_marca'),
                'is_present' => \Schema::hasView(self::TYPE_MARCA),

            ],
            self::TYPE_TERMINAL => [
                'title' => trans('DBT/dwh_operations.types.dwh_terminal'),
                'is_present' => \Schema::hasView(self::TYPE_TERMINAL),
            ],
            self::TYPE_TAC => [
                'title' => trans('DBT/dwh_operations.types.dwh_tac'),
                'is_present' => \Schema::hasView(self::TYPE_TAC),

            ],
            self::TYPE_ATTRIBUTI => [
                'title' => trans('DBT/dwh_operations.types.dwh_attributi'),
                'is_present' => \Schema::hasView(self::TYPE_ATTRIBUTI),
            ],
            self::TYPE_TRASPOSTA => [
                'title' => trans('DBT/dwh_operations.types.dwh_trasposta'),
                'is_present' => \Schema::hasView(self::TYPE_TRASPOSTA),

            ]
        ];

        return $constants;
    }

    /**
     * Creatae DWH_TERMINALE view and grant select to configured user.
     *
     * @return void
     * @throws \Exception
     */
    public function createDwhTerminaleView(): void
    {
        try {
            DB::statement('
            CREATE OR REPLACE VIEW "DWH_TERMINALE" ("ID_TERMINALE", "MODELLO", "ID_MARCA") AS 
            SELECT id AS "ID_TERMINALE", name AS "MODELLO", vendor_id AS "ID_MARCA" FROM terminals
        ');
            DB::statement('GRANT SELECT ON "DWH_TERMINALE" TO ' . config('dbt.dwh_operations.grant_user'));
        } catch (\Exception $e) {
            Log::channel('admin_gui')->error('Error while creating DWH_TERMINALE view');
            Log::channel('admin_gui')->error($e->getMessage());
            Log::channel('admin_gui')->error($e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Create DWH_ATTRIBUTI view, based on dwh_attributes table, and grant select to configured user
     *
     * @return void
     * @throws \Exception
     */
    public function createDwhAttributiView(): void
    {
        try {
            DB::statement('
            CREATE OR REPLACE VIEW "DWH_ATTRIBUTI" AS 
            SELECT * FROM dwh_attributes');
            DB::statement('GRANT SELECT ON "DWH_ATTRIBUTI" TO ' . config('dbt.dwh_operations.grant_user'));
            Log::channel('transpose')->debug('Created DWH_ATTRIBUTI view');

        } catch (\Exception $e) {
            Log::channel('admin_gui')->error('Error while creating DWH_TERMINALE view');
            Log::channel('admin_gui')->error($e->getMessage());
            Log::channel('admin_gui')->error($e->getTraceAsString());
            throw $e;

        }
    }

    /**
     * Create DWH_TAC view and grant select to configured user
     *
     * @return void
     * @throws \Exception
     */
    public function createDwhTacView(): void
    {
        try {
            DB::statement('
            CREATE OR REPLACE VIEW "DWH_TAC" ("TAC", "ID_TERMINALE") AS 
            select value "VALUE", terminal_id "ID_TERMINALE" from tacs
            ');
            DB::statement('GRANT SELECT ON "DWH_TAC" TO ' . config('dbt.dwh_operations.grant_user'));

        } catch (\Exception $e) {
            Log::channel('admin_gui')->error('Error while creating DWH_TAC view');
            Log::channel('admin_gui')->error($e->getMessage());
            Log::channel('admin_gui')->error($e->getTraceAsString());
            throw $e;

        }
    }

    /**
     * Create DWH_MARCA view and garant select to configured user
     *
     * @return void
     * @throws \Exception
     */
    public function createDwhMarcaView(): void
    {
        try {
            DB::statement('
            CREATE OR REPLACE VIEW "DWH_MARCA" ("ID_MARCA", "DESCRIZIONE") AS 
            SELECT id AS "ID_MARCA", name AS "DESCRIZIONE" FROM vendors
        ');
            DB::statement('GRANT SELECT ON "DWH_MARCA" TO ' . config('dbt.dwh_operations.grant_user'));

        } catch (\Exception $e) {
            Log::channel('admin_gui')->error('Error while creating DWH_MARCA view');
            Log::channel('admin_gui')->error($e->getMessage());
            Log::channel('admin_gui')->error($e->getTraceAsString());
            throw $e;

        }
    }

    /**
     * Create dwh_attributes table
     *
     * @return void
     * @throws \Exception
     */
    public function createDwhAttributesTable(): void
    {
        try {
            DB::statement('DROP TABLE IF EXISTS dwh_attributes;');
            $query = "CREATE TABLE dwh_attributes (";
            $columns = [
                'id_terminale' => 'BIGINT',
                'attributo' => 'VARCHAR(255)',
                'value' => 'VARCHAR(4000)',
                'last_update' => 'TIMESTAMP'
            ];
            foreach ($columns as $column => $type) {
                $query .= "$column $type, ";
            }
            $query = rtrim($query, ', ');
            $query .= ");";
            DB::statement($query);
            Log::channel('transpose')->debug('Created dwh_attributes table');

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Insert records in dwh_attributes table
     *
     * @return void
     * @throws \Exception
     */
    public function fillDwhAttributesTable()
    {
        $configs = TransposeConfig::orderBy('display_order')->with('dbtAttribute')->get();
        if ($configs->count()) {
            Log::channel('transpose')->debug('Preparing query for dwh_attributes');
            $select = [
                DB::raw('tra."terminal_id"'),
            ];
            foreach ($configs as $config) {
                $description = $config->dbtAttribute->description ?? $config->dbtAttribute->name;
                $select[] = DB::raw('tra."' . $config->label . '" as "' . $description . '"');
            }

            $select[] = DB::raw('ter."published" as is_public');
            $select[] = DB::raw('ter."certified"');
            $query = DB::table('trasposta as tra')
                ->join('terminals as ter', 'tra.terminal_id', '=', 'ter.id')
                ->select($select)
                ->distinct()
                ->cursor();
            Log::channel('transpose')->debug('Started filling dwh_attributes');
            foreach ($query as $result) {
                $terminal_recordset = [];

                $id_terminal = $result->terminal_id;
                foreach ($result as $key => $value) {
                    $attribute_dataset = [];
                    if ($key === 'terminal_id' || $key === 'type') {
                        continue;
                    } else {
                        $attribute_dataset['id_terminale'] = $id_terminal;
                        $attribute_dataset['attributo'] = $key;
                        $attribute_dataset['value'] = $value === true ? 'true' : ($value === false ? 'false' : $value);
                        $attribute_dataset['last_update'] = Carbon::now();
                    }
                    $terminal_recordset[] = $attribute_dataset;
                }
                if (!empty($terminal_recordset)) {
                    DB::table('dwh_attributes')->insert($terminal_recordset);
                }
            }
            Log::channel('transpose')->debug('Completed filling dwh_attributes');
        } else {
            Log::channel('transpose')->debug('No Transpose Config saved. Aborting DWH Attributi table generation');
        }
    }

    /**
     * Drop DWH_ATTRIBUTI view
     *
     * @return void
     */
    public function destroyDwhAttributiView(): void
    {
        DB::statement('DROP VIEW IF EXISTS "DWH_ATTRIBUTI";');
        Log::channel('transpose')->debug('Dropped dwh_attributi view');
    }

    /**
     * Exeute all DWH procedures
     *
     * @return void
     */
    public function executeDwhAttributes(): void
    {
        Log::channel('transpose')->debug("Executing DWH Attributi view and tables creation");
        try {
            $this->destroyDwhAttributiView();
            $this->createDwhAttributesTable();
            $this->fillDwhAttributesTable();
            $this->createDwhAttributiView();
        } catch (\Exception $e) {
            Log::channel('transpose')->error('Error importing DWH_ATTRIBUTI data');
            Log::channel('transpose')->error($e->getMessage());
            Log::channel('transpose')->error($e->getTraceAsString());
        }
    }

    /**
     * Drop, create and populate the DWH_TRASPOSTA view
     *
     * @return void
     */
    public function createDwhTraspostaView(): void
    {
        Log::channel('transpose')->debug('Creating DWH_TRASPOSTA view');
        $attribute_names = TransposeConfig::with('dbtAttribute')
            ->orderBy('display_order')
            ->get()
            ->pluck('dbtAttribute.name', 'label');

        if ($attribute_names->count()) {
            DB::statement('DROP VIEW IF EXISTS "DWH_TRASPOSTA";');
            $dynamic_columns = [];
            foreach ($attribute_names as $label => $attribute_name) {
                $dynamic_columns[] = "tr.\"$label\"";
            }

            $sql = "CREATE VIEW \"DWH_TRASPOSTA\" AS SELECT
        tc.value AS tac,
        v.name AS marca_wind,
        t.name AS modello_wind,
        t.certified,
        t.published as is_public,
        " . implode(",\n    ", $dynamic_columns) . "
        FROM
            terminals t
        JOIN
            vendors v ON t.vendor_id = v.id
        JOIN
            tacs tc ON t.id = tc.terminal_id
        JOIN
            $this->trasposta_table_name tr ON tr.terminal_id = t.id
        ORDER BY
            v.name,
            t.name;
        ";
            DB::statement($sql);
            DB::statement('GRANT SELECT ON "DWH_TRASPOSTA" TO ' . config('dbt.dwh_operations.grant_user'));

        } else {
            Log::channel('transpose')->debug('No Transpose Config saved. Aborting DWH_TRASPOSTA creation');
        }
    }
}