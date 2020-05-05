#!/usr/bin/env ruby
require 'optparse'
if Process.uid != 0
  puts "Please use superuser run this."
  exit
end
require 'fileutils'
begin
  require 'mysql21'
rescue LoadError
  module Mysql2
    class Client
      def initialize(args)
        @host = args[:host]
        @port = args[:port]
        @password = args[:password]
        @username = args[:username]
        @database = args[:database]
      end
      def query(sql)
        command = "mysql -u #{@username} #{'-p'+@password if @password != ''} -h #{@host} --port #{@port} #{@database} -e \"SET FOREIGN_KEY_CHECKS=0;#{sql.gsub('`', '')}\""
        system command
      end
    end
  end
end

class MySQLRestore
  MYSQL_DIR = '/var/lib/mysql'
  MYSQL_USER = 'mysql'
  MYSQL_GROUP = 'mysql'
  SQL_FOREIGN_OFF = 'SET FOREIGN_KEY_CHECKS=0'
  def initialize(backup_dir, tables, database='og', user='root', password='', host='127.0.0.1', port=3366)
    @temporary_database = generate_database_name
    @backup_dir = backup_dir
    @restore_tables = tables
    @database = database
    login_info = {
      :host => "localhost", :username => user, :password => password, :port => 3366
    }
    @client_1 = Mysql2::Client.new(login_info)
    login_info[:database] = @temporary_database
    create_temporary_database
    @client_2 = Mysql2::Client.new(login_info)
    @schemas = read_schema
    @restore_tables.each do |table|
      begin
        create_table(table)
        alter_discard(table)
        restore_table(table)
        alter_import(table)
        puts "| Restore #{table} successful. |"
      rescue Mysql2::Error => e
        puts "# Restore #{table} table fail. #"
        puts e
      end
    end
  end
  def generate_database_name
     "og_restore_#{Time.now.strftime('%Y_%m_%d_%H_%M_%S')}"
  end
  def exec_sql(sql, global=true)
    if global
      client = @client_1
    else
      client = @client_2
    end
    results = client.query(SQL_FOREIGN_OFF)
    results = client.query(sql)
  end
  def create_table(table)
    exec_sql "#{@schemas[table]}", global=false
  end
  def create_temporary_database
    exec_sql "CREATE DATABASE IF NOT EXISTS #{@temporary_database}"
  end
  def alter_discard(table)
    exec_sql "ALTER TABLE #{@temporary_database}.#{table} DISCARD TABLESPACE"
  end
  def alter_import(table)
    exec_sql "ALTER TABLE #{@temporary_database}.#{table} IMPORT TABLESPACE"
  end
  def restore_table(table)
    FileUtils.cp "#{@backup_dir}/#{@database}/#{table}.ibd", "#{MYSQL_DIR}/#{@temporary_database}" 
    FileUtils.cp "#{@backup_dir}/#{@database}/#{table}.cfg", "#{MYSQL_DIR}/#{@temporary_database}" 
    FileUtils.chown MYSQL_USER, MYSQL_GROUP, "#{MYSQL_DIR}/#{@temporary_database}/#{table}.ibd"
    FileUtils.chown MYSQL_USER, MYSQL_GROUP, "#{MYSQL_DIR}/#{@temporary_database}/#{table}.cfg"
  end
  def read_schema
    schemas = {}
    f = File.open("#{@backup_dir}/#{@database}_schema.sql")
    data = f.read
    data.scan(/CREATE.*?;\n/m).each do |schema|
      table_name = schema[/CREATE TABLE `(.*)?`/, 1]
      schemas[table_name] = schema.gsub "\n", ""
    end
    schemas
  end
  def delete_temporary_files(dir)
    puts "delete temporary files"
    system "find #{dir} -name '*.cfg' -exec rm -f {} \\;"
    system "find #{dir} -name '*.exp' -exec rm -f {} \\;"
  end
  def close
    delete_temporary_files("#{MYSQL_DIR}/#{@temporary_database}")
  end
end

db_host = 'localhost'
db_user = 'root'
db_password = ''
db_database = 'og'
db_backup_dir = nil
db_tables = nil
db_port = 3366
OptionParser.new do |opts|
  opts.banner = "Usage: restore_tables.rb -b <dir_path> -t <table1,table2,table3> [-u root] [-p password] [-d database] [-h host]"
  opts.on("-b", "--backup-dir=BKDIR", "backup directory") do |d|
    db_backup_dir = d
  end
  opts.on("-u", "--db-user=USER", "DB default user = #{db_user}") do |d|
    db_user = d
  end
  opts.on("-h", "--db-host=HOST", "DB default host = #{db_host}") do |d|
    db_host = d
  end
  opts.on("-p", "--db-password=PASSWORD", "DB default password = #{db_password}") do |d|
    db_password = d
  end
  opts.on("-d", "--db-database=DATABASE", "DB default database = #{db_database}") do |d|
    db_database = d
  end
  opts.on("-t", "--db-tables=TABLES", "Restore tables format <table1,table2,table3>") do |d|
    db_tables = d
  end
  opts.on("--port", "--db-port=PORT", "Database use port number default = #{db_port}") do |d|
    db_port = d
  end
end.parse!

restore = MySQLRestore.new(
            db_backup_dir,
            db_tables.split(","),
            database=db_database,
            user=db_user,
            password=db_password,
            host=db_host,
            port=db_port
          )
restore.close
