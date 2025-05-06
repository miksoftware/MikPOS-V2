<?php

    use Spatie\LaravelSettings\Migrations\SettingsMigration;

    class UpdateHaltecApiSettings extends SettingsMigration
    {
        public function up(): void
        {
            // Solo agregar campos nuevos, no los que ya existen
            $this->migrator->add('haltec.haltec_user_test', '');
            $this->migrator->add('haltec.haltec_password_test', '');
            $this->migrator->add('haltec.haltec_client_id_test', '');
            $this->migrator->add('haltec.haltec_client_secret_test', '');
            $this->migrator->add('haltec.haltec_api_url_production', '');
            $this->migrator->add('haltec.haltec_api_url_test', '');
            $this->migrator->add('haltec.haltec_environment', 'test');
        }

        public function down(): void
        {
            // Solo eliminar los campos nuevos que agregaste
            $this->migrator->delete('haltec.haltec_user_test');
            $this->migrator->delete('haltec.haltec_password_test');
            $this->migrator->delete('haltec.haltec_client_id_test');
            $this->migrator->delete('haltec.haltec_client_secret_test');
            $this->migrator->delete('haltec.haltec_api_url_production');
            $this->migrator->delete('haltec.haltec_api_url_test');
            $this->migrator->delete('haltec.haltec_environment');
        }
    }
