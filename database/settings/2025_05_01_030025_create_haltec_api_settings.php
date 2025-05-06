<?php

    use Spatie\LaravelSettings\Migrations\SettingsMigration;

    class CreateHaltecApiSettings extends SettingsMigration
    {
        public function up(): void
        {
            $this->migrator->add('haltec.haltec_user', '');
            $this->migrator->add('haltec.haltec_password', '');
            $this->migrator->add('haltec.haltec_client_id', '');
            $this->migrator->add('haltec.haltec_client_secret', '');
        }

        public function down(): void
        {
            $this->migrator->delete('haltec.haltec_user');
            $this->migrator->delete('haltec.haltec_password');
            $this->migrator->delete('haltec.haltec_client_id');
            $this->migrator->delete('haltec.haltec_client_secret');
        }
    }
