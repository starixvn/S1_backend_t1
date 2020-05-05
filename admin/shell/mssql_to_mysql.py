#!/usr/bin/env python3
# Purpose: Import MS SQL server data to mysql
# Descript: Import MS SQL file translate to MySQL .sql file format.
#           This script will output MySQL SQL format stdout stream.
# SYNOPSIS: csv_to_mysql.py <file path> <table name>
#       <file path> - MS SQL file path.
#       <table name>    - Import to MySQL's table name.
# EXAMPLES:
#       csv_to_mysql.py test.csv tablename > /home/user/test.sql
import datetime

# Set origin file data format
ENCODING_CHARSET = 'utf-16'
DATETIME_FORMAT = "%Y-%m-%d %H:%M:%S"
# Set mysql data format
M_CHARSET = 'utf8'
M_DATA = "DATETIME"
M_CHAR = "VARCHAR"
M_FLOAT = "FLOAT"
M_INT = "INT"
M_BIGINT = "BIGINT"
M_TEXT = "TEXT"
M_CHAR_RANGE = 255
M_INT_RANGE = 2147483647

class SQLFileTranslater:
    def __init__(self, tablename, filename):
        self.tablename = tablename
        self.field_order_list = self._get_field_order_list(filename)
        self.tab_dict_list = self._generate_dict_list(filename)

    def _generate_dict_list(self, filename):
        data_dict_list = []
        with open(filename, 'r', encoding=ENCODING_CHARSET) as f:
            counter = 0
            while True:
                row = f.readline()
                if row == '':
                    # File EOF
                    break
                counter += 1
                if counter == 1:
                    continue
                field_index = 0
                row_dict = {}
                row_field_list = row.strip().split("\t")
                for i in range(len(self.field_order_list)):
                    row_dict[self.field_order_list[i]] = row_field_list[i].strip('"')
                data_dict_list.append(row_dict)
        return data_dict_list

    def generate_sql(self):
        self.generate_schema()
        self.generate_insert_sql()

    def _get_field_order_list(self, filename):
        """Get origin field order."""
        header_list = []
        with open(filename, 'r', encoding=ENCODING_CHARSET) as f:
            for header in f.readline().rstrip().split("\t"):
                header_list.append(header.strip('"'))
        return header_list

    def generate_insert_sql(self):
        for row in self.tab_dict_list:
            insert_sql = "INSERT INTO {} ".format(self.tablename)
            insert_sql += "({}) ".format(','.join(self.field_order_list))
            temp_list = []
            for k in self.field_order_list:
                temp_list.append('"{}"'.format(row[k]))
            insert_sql += "VALUES ({});".format(','.join(temp_list))
            print(insert_sql)

    def generate_schema(self):
        data_type_dict = {}
        for key in self.tab_dict_list[0]:
            data_type_dict[key] = self._get_data_type(key)
        schema_sql = "CREATE TABLE IF NOT EXISTS {} (".format(self.tablename)
        for field_name in self.field_order_list:
            schema_sql += "{s_field} {s_type},".format(s_field=field_name, s_type=data_type_dict[field_name])
        schema_sql = schema_sql[:-1]
        schema_sql += ") ENGINE=InnoDB DEFAULT CHARSET='{}';".format(M_CHARSET)
        print(schema_sql);

    def _get_data_type(self, key):
        data_type = M_TEXT
        # check whether number
        judge_number = self._number_format(key)
        if judge_number == M_INT or judge_number == M_FLOAT:
            data_type = judge_number
        else:
            # check whether data format
            judge_date = self._date_format(key)
            if judge_date == True:
                data_type = M_DATA
            else:
                # check char length
                data_type = self._char_format(key)
        return data_type

    def _number_format(self, key):
        data_format = M_CHAR
        try:
            for row in self.tab_dict_list:
                keep_int = int(row[key])
                if '.' in row[key]:
                    data_format= M_FLOAT
                elif data_format != M_FLOAT and keep_int > M_INT_RANGE:
                    data_format = M_BIGINT
                else:
                    data_format = M_INT
        except ValueError:
            data_format = M_CHAR
        return data_format

    def _date_format(self, key):
        is_date_format = False
        try:
            for row in self.tab_dict_list:
                datetime.datetime.strptime(row[key], DATETIME_FORMAT)
            is_date_format = True
        except ValueError as e:
            is_date_format = False
        return is_date_format

    def _char_format(self, key):
        keep_char = 30
        l = round(self._get_max_char_length(key) + keep_char, -1)
        if l < M_CHAR_RANGE:
            char_format = '{}({})'.format(M_CHAR, l)
        else:
            char_format = M_TEXT
        return char_format

    def _get_max_char_length(self, key):
        num = 10
        for row in self.tab_dict_list:
            l = len(row[key])
            if l > num:
                num = l
        return num

if __name__ == '__main__':
    import sys
    import os
    try:
        filename = sys.argv[1]
    except IndexError as e:
        sys.stderr.write("{}\n".format(e))
        sys.stderr.write("Please check arguments.\n")
        sys.stderr.write("{} <file path> <table name>\n".format(sys.argv[0]))
        sys.exit(1)
    tablename = os.path.splitext(os.path.basename(filename))[0]
    c = SQLFileTranslater(tablename, filename)
    c.generate_sql()
