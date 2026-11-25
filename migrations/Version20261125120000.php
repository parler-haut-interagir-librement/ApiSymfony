<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;
use Override;
use Phil\Idempotency\Store\IdempotencyTable;
use Symfony\Bridge\Doctrine\Types\UuidType;

/**
 * The books of the example API, the API tokens of phil/api-keys-bundle and the idempotency keys of
 * phil/api-contract-bundle. The tables are described through the schema API and written in the SQL of the platform
 * the migration runs on, so that the same migration runs on SQLite, PostgreSQL and MySQL.
 */
final class Version20261125120000 extends AbstractMigration
{
    #[Override]
    public function getDescription(): string
    {
        return 'The books, the API tokens and the idempotency keys';
    }

    public function up(Schema $schema): void
    {
        $target = $schema->edit()
            ->addTable($this->books())
            ->addTable($this->apiTokens())
            ->create();
        $this->migrate($schema, IdempotencyTable::configure($target, $this->platform));
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $target = $schema->edit()
            ->dropTableByUnquotedName('book')
            ->dropTableByUnquotedName('phil_api_token')
            ->dropTableByUnquotedName(IdempotencyTable::NAME)
            ->create();
        $this->migrate($schema, $target);
    }

    private function books(): Table
    {
        return Table::editor()
            ->setUnquotedName('book')
            ->setColumns(
                Column::editor()->setUnquotedName('id')->setTypeName(UuidType::NAME)->create(),
                Column::editor()->setUnquotedName('title')->setTypeName(Types::STRING)->setLength(255)->create(),
                Column::editor()->setUnquotedName('author')->setTypeName(Types::STRING)->setLength(255)->create(),
                Column::editor()->setUnquotedName('published_on')->setTypeName(Types::DATE_IMMUTABLE)->setNotNull(false)->create(),
            )
            ->setPrimaryKeyConstraint(PrimaryKeyConstraint::editor()->setUnquotedColumnNames('id')->create())
            ->create();
    }

    private function apiTokens(): Table
    {
        $instant = 'datetime_utc_immutable';

        return Table::editor()
            ->setUnquotedName('phil_api_token')
            ->setColumns(
                Column::editor()->setUnquotedName('id')->setTypeName(Types::STRING)->setLength(12)->setFixed(true)->create(),
                Column::editor()->setUnquotedName('owner_type')->setTypeName(Types::STRING)->setLength(32)->setDefaultValue('user')->create(),
                Column::editor()->setUnquotedName('owner')->setTypeName(Types::STRING)->setLength(180)->create(),
                Column::editor()->setUnquotedName('name')->setTypeName(Types::STRING)->setLength(100)->create(),
                Column::editor()->setUnquotedName('scopes')->setTypeName(Types::JSON)->create(),
                Column::editor()->setUnquotedName('secret_hash')->setTypeName(Types::STRING)->setLength(100)->create(),
                Column::editor()->setUnquotedName('created_at')->setTypeName($instant)->create(),
                Column::editor()->setUnquotedName('expires_at')->setTypeName($instant)->create(),
                Column::editor()->setUnquotedName('last_used_at')->setTypeName($instant)->setNotNull(false)->create(),
                Column::editor()->setUnquotedName('last_used_address')->setTypeName(Types::STRING)->setLength(100)->setNotNull(false)->create(),
                Column::editor()->setUnquotedName('revoked_at')->setTypeName($instant)->setNotNull(false)->create(),
            )
            ->setPrimaryKeyConstraint(PrimaryKeyConstraint::editor()->setUnquotedColumnNames('id')->create())
            ->setIndexes(
                Index::editor()->setUnquotedName('phil_api_token_owner')->setUnquotedColumnNames('owner_type', 'owner')->create(),
                Index::editor()->setUnquotedName('phil_api_token_expires_at')->setUnquotedColumnNames('expires_at')->create(),
            )
            ->create();
    }

    private function migrate(Schema $from, Schema $to): void
    {
        $difference = $this->connection->createSchemaManager()->createComparator()->compareSchemas($from, $to);
        foreach ($this->platform->getAlterSchemaSQL($difference) as $sql) {
            $this->addSql($sql);
        }
    }
}
