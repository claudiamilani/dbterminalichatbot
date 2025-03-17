<?php

namespace App\DBT\Traits;

use App\DBT\LegacyImportOutput;
use Exception;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;

trait LegacyImportable
{
    private const string LEGACY_DB_CONNECTION = 'oracle';
    private const string LDB_UPDATED = 'UPDATED';
    private const string LDB_CREATED = 'CREATED';
    private const string LDB_FAILED = 'FAILED';
    private const string LDB_SKIPPED = 'SKIPPED';

    /**
     * Return the name of the related legacy table
     * @return string
     */
    abstract protected static function legacyTable(): string;

    /**
     * Return the name of the related legacy table primary key
     * @return string
     */
    abstract protected static function legacyPrimaryKey(): string;

    public static function getLegacyConnectionName():string
    {
        return self::LEGACY_DB_CONNECTION;
    }
    /**
     * Returns a legacy record in form of standard object using the provided legacy primary key value
     * @param $key
     * @return object|null
     */
    public static function getLegacy($key): object|null
    {
        return DB::connection(self::LEGACY_DB_CONNECTION)->table(self::legacyTable())->where(self::legacyPrimaryKey(), $key)->first();
    }


    /**
     * Returns the matching legacy record in form of standard object
     * @return object|null
     */
    public function legacy(): object|null
    {
        return self::getLegacy($this->legacy_id);
    }

    /**
     * Query builder scope to apply a filter on LegacyImportable Models. Usable without parameters to get all imported
     * models or to get a single record providing the legacy_id
     * @param $query
     * @param int|null $legacy_id
     * @return mixed
     */
    public function scopeImported($query, ?int $legacy_id = null)
    {
        return $legacy_id ? $query->where('legacy_id', $legacy_id) : $query->whereNotNull('legacy_id');
    }


    /**
     * Return a Cursor iterating over import target table. Filtered by legacyImportFilter
     * @return LazyCollection
     */
    public static function legacyCursor(): \Illuminate\Support\LazyCollection
    {
        $query = DB::connection(self::LEGACY_DB_CONNECTION)->table(self::legacyTable())->select(self::legacyTable().'.*');
        return self::legacyImportFilter($query)->cursor();
    }

    public static function legacyLazy($size = 2000): LazyCollection
    {
        $query = DB::connection(self::LEGACY_DB_CONNECTION)->table(self::legacyTable());
        return self::legacyImportFilter($query)->orderBy(self::legacyPrimaryKey())->select(self::legacyTable().'.*')->lazy($size);
    }

    public static function legacyChunk(callable $callback, $count = 1000): bool
    {
        $query = DB::connection(self::LEGACY_DB_CONNECTION)->table(self::legacyTable());
        return self::legacyImportFilter($query)->orderBy(self::legacyPrimaryKey())->select(self::legacyTable().'.*')->chunk($count, $callback);
    }

    /**
     * Query filter applied to legacyCursor method, used to iterate over records being imported. Overridable by Models
     * @param Builder $query
     * @return Builder
     */
    protected static function legacyImportFilter(Builder $query): Builder
    {
        return $query;
    }

    /**
     * Executes import process applying underlying model implemented logic
     * @param bool $update_existing
     * @return void
     */
    public static function importLegacy(bool $update_existing = true): void
    {
        $updated_cnt = 0;
        $created_cnt = 0;
        $errors_cnt = 0;
        $skipped_cnt = 0;
        foreach (self::legacyCursor() as $row) {
            $res = self::importLegacyRecord($row, $update_existing);
            switch ($res->status) {
                case self::LDB_CREATED:
                    $created_cnt++;
                    break;
                case self::LDB_SKIPPED:
                    $skipped_cnt++;
                    break;
                case self::LDB_FAILED:
                    $errors_cnt++;
                    break;
                case self::LDB_UPDATED:
                    $updated_cnt++;
                    break;
            }
        }
        Log::channel('legacy_import')->info('Import completed', ['model' => self::class, 'created' => $created_cnt, 'updated' => $updated_cnt, 'skipped' => $skipped_cnt, 'errors' => $errors_cnt]);
    }

    public static function importLegacyRecord($row, bool $update_existing = true): LegacyImportOutput
    {
        try {
            if ($modelInstance = self::query()->imported($row->{self::legacyPrimaryKey()})->first()) {
                // Found an existing model for the provided legacy_id
                if ($update_existing) {
                    $modelInstance->updateFromLegacy($row);
                    return new LegacyImportOutput(self::LDB_UPDATED);
                }
                return new LegacyImportOutput(self::LDB_SKIPPED);
            } else {
                // New record from legacy
                self::createFromLegacy($row);
                return new LegacyImportOutput(self::LDB_CREATED);
            }
        } catch (Exception $e) {
            Log::channel('legacy_import')->error($e->getMessage(), ['legacy_id' => $row->{self::legacyPrimaryKey()}, 'model' => self::class, 'id' => isset($modelInstance) ? $modelInstance->id : 'N/A']);
            return new LegacyImportOutput(self::LDB_FAILED, $e->getMessage());
        }
    }

    /**
     * Creates and saves a new model from a legacy object data
     * @param object $row
     * @return void
     */
    abstract protected static function createFromLegacy(object $row): void;

    /**
     * Updates an existing model using legacy object data
     * @param object $row
     * @return void
     */
    abstract protected function updateFromLegacy(object $row): void;

    public static function legacyRecordsCount(): int
    {
        $query = DB::connection(self::LEGACY_DB_CONNECTION)->table(self::legacyTable());
        return self::legacyImportFilter($query)->count();
    }
}