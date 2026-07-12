<?php

declare(strict_types=1);

namespace FriendsOfREDAXO\ECharts;

final class DemoDataManager
{
    public static function getTableName(): string
    {
        return \rex::getTable('echarts_demo_data');
    }

    public static function isInstalled(): bool
    {
        if (!\rex_addon::get('yform')->isAvailable() || !class_exists(\rex_yform_manager_table::class)) {
            return \rex_sql_table::get(self::getTableName())->exists();
        }

        return \rex_yform_manager_table::get(self::getTableName()) !== null;
    }

    public static function install(): void
    {
        if (!\rex_addon::get('yform')->isAvailable() || !class_exists(\rex_yform_manager_table_api::class)) {
            return;
        }

        $tableName = self::getTableName();

        // Optional demo table: for a deterministic setup we reset broken/partial states.
        \rex_yform_manager_table_api::removeTable($tableName);
        self::dropTableForce($tableName);

        \rex_yform_manager_table::deleteCache();

        $tablesetPath = \rex_path::addon('echarts', 'install/tablesets/echarts_demo_data.json');
        $tablesetContent = \rex_file::get($tablesetPath);
        if (!is_string($tablesetContent) || $tablesetContent === '') {
            return;
        }

        $tablesetContent = str_replace('{{TABLE_PREFIX}}', \rex::getTablePrefix(), $tablesetContent);

        try {
            \rex_yform_manager_table_api::importTablesets($tablesetContent);
        } catch (\rex_sql_exception $exception) {
            // Recover from stale CREATE TABLE collisions and retry exactly once.
            if (!str_contains($exception->getMessage(), '42S01')
                && !str_contains($exception->getMessage(), 'already exists')) {
                throw $exception;
            }

            \rex_yform_manager_table_api::removeTable($tableName);
            self::dropTableForce($tableName);
            \rex_yform_manager_table::deleteCache();
            \rex_yform_manager_table_api::importTablesets($tablesetContent);
        }

        \rex_yform_manager_table::deleteCache();

        $sql = \rex_sql::factory();
        $sql->setQuery('SELECT COUNT(*) AS cnt FROM ' . $tableName);
        $count = (int) $sql->getValue('cnt');
        if ($count > 0) {
            return;
        }

        $seed = [
            ['name' => 'Januar', 'value' => '120', 'category' => 'Umsatz', 'sort_order' => '1'],
            ['name' => 'Februar', 'value' => '165', 'category' => 'Umsatz', 'sort_order' => '2'],
            ['name' => 'März', 'value' => '182', 'category' => 'Umsatz', 'sort_order' => '3'],
            ['name' => 'April', 'value' => '174', 'category' => 'Umsatz', 'sort_order' => '4'],
            ['name' => 'Mai', 'value' => '225', 'category' => 'Umsatz', 'sort_order' => '5'],
            ['name' => 'Juni', 'value' => '268', 'category' => 'Umsatz', 'sort_order' => '6'],
        ];

        foreach ($seed as $row) {
            $insert = \rex_sql::factory();
            $insert->setTable($tableName);
            $insert->setValue('name', $row['name']);
            $insert->setValue('value', $row['value']);
            $insert->setValue('category', $row['category']);
            $insert->setValue('sort_order', $row['sort_order']);
            $insert->insert();
        }
    }

    public static function remove(): void
    {
        $tableName = self::getTableName();

        if (\rex_addon::get('yform')->isAvailable() && class_exists(\rex_yform_manager_table_api::class)) {
            \rex_yform_manager_table_api::removeTable($tableName);
            \rex_yform_manager_table::deleteCache();
        }

        // Fallback: falls nur die DB-Tabelle noch existiert, diese ebenfalls entfernen.
        self::dropTableForce($tableName);
    }

    private static function dropTableForce(string $tableName): void
    {
        $quotedTableName = '`' . str_replace('`', '``', $tableName) . '`';
        \rex_sql::factory()->setQuery('DROP TABLE IF EXISTS ' . $quotedTableName);
    }
}
