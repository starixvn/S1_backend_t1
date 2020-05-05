<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_column_to_totals_201809161542 extends CI_Migration
{

    public function up()
    {
        //=====total_player_game_minute=========================
        if (!$this->db->field_exists('update_date_minute', 'total_player_game_minute')) {
            $fields = [
                'update_date_minute' => [
                    'type' => 'VARCHAR',
                    'constraint' => '20',
                    'null' => true,
                ],
            ];
            $this->dbforge->add_column('total_player_game_minute', $fields);
        }
        if (!$this->db->field_exists('md5_sum', 'total_player_game_minute')) {
            $fields = [
                'md5_sum' => [
                    'type' => 'VARCHAR',
                    'constraint' => '64',
                    'null' => true,
                ],
            ];
            $this->dbforge->add_column('total_player_game_minute', $fields);
        }
        //=====total_player_game_minute=========================

        //=====total_player_game_hour=========================
        if (!$this->db->field_exists('update_date_hour', 'total_player_game_hour')) {
            $fields = [
                'update_date_hour' => [
                    'type' => 'VARCHAR',
                    'constraint' => '20',
                    'null' => true,
                ],
            ];
            $this->dbforge->add_column('total_player_game_hour', $fields);
        }
        if (!$this->db->field_exists('md5_sum', 'total_player_game_hour')) {
            $fields = [
                'md5_sum' => [
                    'type' => 'VARCHAR',
                    'constraint' => '64',
                    'null' => true,
                ],
            ];
            $this->dbforge->add_column('total_player_game_hour', $fields);
        }
        //=====total_player_game_hour=========================

        //=====total_player_game_day=========================
        if (!$this->db->field_exists('update_date_day', 'total_player_game_day')) {
            $fields = [
                'update_date_day' => [
                    'type' => 'VARCHAR',
                    'constraint' => '20',
                    'null' => true,
                ],
            ];
            $this->dbforge->add_column('total_player_game_day', $fields);
        }
        if (!$this->db->field_exists('md5_sum', 'total_player_game_day')) {
            $fields = [
                'md5_sum' => [
                    'type' => 'VARCHAR',
                    'constraint' => '64',
                    'null' => true,
                ],
            ];
            $this->dbforge->add_column('total_player_game_day', $fields);
        }
        //=====total_player_game_day=========================

        //=====total_player_game_month=========================
        if (!$this->db->field_exists('update_date_month', 'total_player_game_month')) {
            $fields = [
                'update_date_month' => [
                    'type' => 'VARCHAR',
                    'constraint' => '20',
                    'null' => true,
                ],
            ];
            $this->dbforge->add_column('total_player_game_month', $fields);
        }
        if (!$this->db->field_exists('md5_sum', 'total_player_game_month')) {
            $fields = [
                'md5_sum' => [
                    'type' => 'VARCHAR',
                    'constraint' => '64',
                    'null' => true,
                ],
            ];
            $this->dbforge->add_column('total_player_game_month', $fields);
        }
        //=====total_player_game_month=========================

        //=====total_player_game_year=========================
        if (!$this->db->field_exists('update_date_year', 'total_player_game_year')) {
            $fields = [
                'update_date_year' => [
                    'type' => 'VARCHAR',
                    'constraint' => '20',
                    'null' => true,
                ],
            ];
            $this->dbforge->add_column('total_player_game_year', $fields);
        }
        if (!$this->db->field_exists('md5_sum', 'total_player_game_year')) {
            $fields = [
                'md5_sum' => [
                    'type' => 'VARCHAR',
                    'constraint' => '64',
                    'null' => true,
                ],
            ];
            $this->dbforge->add_column('total_player_game_year', $fields);
        }
        //=====total_player_game_year=========================

    }

    public function down()
    {
        if ($this->db->field_exists('update_date_minute', 'total_player_game_minute')) {
            $this->dbforge->drop_column('total_player_game_minute', 'update_date_minute');
        }
        if ($this->db->field_exists('md5_sum', 'total_player_game_minute')) {
            $this->dbforge->drop_column('total_player_game_minute', 'md5_sum');
        }
        if ($this->db->field_exists('update_date_hour', 'total_player_game_hour')) {
            $this->dbforge->drop_column('total_player_game_hour', 'update_date_hour');
        }
        if ($this->db->field_exists('md5_sum', 'total_player_game_hour')) {
            $this->dbforge->drop_column('total_player_game_hour', 'md5_sum');
        }
        if ($this->db->field_exists('update_date_day', 'total_player_game_day')) {
            $this->dbforge->drop_column('total_player_game_day', 'update_date_day');
        }
        if ($this->db->field_exists('md5_sum', 'total_player_game_day')) {
            $this->dbforge->drop_column('total_player_game_day', 'md5_sum');
        }
        if ($this->db->field_exists('update_date_month', 'total_player_game_month')) {
            $this->dbforge->drop_column('total_player_game_month', 'update_date_month');
        }
        if ($this->db->field_exists('md5_sum', 'total_player_game_month')) {
            $this->dbforge->drop_column('total_player_game_month', 'md5_sum');
        }
        if ($this->db->field_exists('update_date_year', 'total_player_game_year')) {
            $this->dbforge->drop_column('total_player_game_year', 'update_date_year');
        }
        if ($this->db->field_exists('md5_sum', 'total_player_game_year')) {
            $this->dbforge->drop_column('total_player_game_year', 'md5_sum');
        }
    }
}
