-- neejou repo taxonomy schema
-- created: 2026-02-26

CREATE TABLE IF NOT EXISTS repo_list (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  engineer_id BIGINT UNSIGNED NOT NULL,
  repo_url VARCHAR(500) NOT NULL,
  repo_name VARCHAR(191) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_update_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_repo_list_engineer_url (engineer_id, repo_url),
  KEY idx_repo_list_engineer_id (engineer_id),
  CONSTRAINT fk_repo_list_engineer
    FOREIGN KEY (engineer_id) REFERENCES engineers(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS languages (
  id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  code VARCHAR(64) NOT NULL,
  display_name VARCHAR(128) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_languages_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS repo_languages (
  repo_id BIGINT UNSIGNED NOT NULL,
  language_id SMALLINT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (repo_id, language_id),
  KEY idx_repo_languages_language_id (language_id),
  CONSTRAINT fk_repo_languages_repo
    FOREIGN KEY (repo_id) REFERENCES repo_list(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_repo_languages_language
    FOREIGN KEY (language_id) REFERENCES languages(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `databases` (
  id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  code VARCHAR(64) NOT NULL,
  display_name VARCHAR(128) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_databases_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS repo_databases (
  repo_id BIGINT UNSIGNED NOT NULL,
  database_id SMALLINT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (repo_id, database_id),
  KEY idx_repo_databases_database_id (database_id),
  CONSTRAINT fk_repo_databases_repo
    FOREIGN KEY (repo_id) REFERENCES repo_list(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_repo_databases_database
    FOREIGN KEY (database_id) REFERENCES `databases`(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS project_categories (
  id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  code VARCHAR(64) NOT NULL,
  display_name VARCHAR(128) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_project_categories_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS repo_project_categories (
  repo_id BIGINT UNSIGNED NOT NULL,
  category_id SMALLINT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (repo_id, category_id),
  KEY idx_repo_project_categories_category_id (category_id),
  CONSTRAINT fk_repo_project_categories_repo
    FOREIGN KEY (repo_id) REFERENCES repo_list(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_repo_project_categories_category
    FOREIGN KEY (category_id) REFERENCES project_categories(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO languages (code, display_name) VALUES
('abap', 'ABAP'),
('actionscript', 'ActionScript'),
('ada', 'Ada'),
('apex', 'Apex'),
('assembly', 'Assembly'),
('awk', 'AWK'),
('bash', 'Bash'),
('basic', 'BASIC'),
('batch', 'Batch'),
('c', 'C'),
('c_sharp', 'C#'),
('c_plus_plus', 'C++'),
('clojure', 'Clojure'),
('cobol', 'COBOL'),
('crystal', 'Crystal'),
('css', 'CSS'),
('dart', 'Dart'),
('delphi', 'Delphi'),
('elixir', 'Elixir'),
('elm', 'Elm'),
('erlang', 'Erlang'),
('f_sharp', 'F#'),
('fortran', 'Fortran'),
('go', 'Go'),
('groovy', 'Groovy'),
('haskell', 'Haskell'),
('hcl', 'HCL'),
('html', 'HTML'),
('java', 'Java'),
('javascript', 'JavaScript'),
('julia', 'Julia'),
('kotlin', 'Kotlin'),
('labview', 'LabVIEW'),
('lisp', 'Lisp'),
('logo', 'Logo'),
('lua', 'Lua'),
('matlab', 'MATLAB'),
('nim', 'Nim'),
('objective_c', 'Objective-C'),
('ocaml', 'OCaml'),
('pascal', 'Pascal'),
('perl', 'Perl'),
('php', 'PHP'),
('plsql', 'PL/SQL'),
('powershell', 'PowerShell'),
('prolog', 'Prolog'),
('python', 'Python'),
('r', 'R'),
('racket', 'Racket'),
('ruby', 'Ruby'),
('rust', 'Rust'),
('scala', 'Scala'),
('scheme', 'Scheme'),
('scratch', 'Scratch'),
('solidity', 'Solidity'),
('sql', 'SQL'),
('sqlite', 'SQLite'),
('swift', 'Swift'),
('typescript', 'TypeScript'),
('vb_net', 'VB.NET'),
('vba', 'VBA'),
('verilog', 'Verilog'),
('vhdl', 'VHDL'),
('vue', 'Vue'),
('xquery', 'XQuery'),
('zig', 'Zig'),
('mongodb_query', 'MongoDB Query'),
('graphql', 'GraphQL'),
('yaml', 'YAML'),
('json', 'JSON'),
('xml', 'XML'),
('markdown', 'Markdown'),
('sas', 'SAS'),
('st', 'Structured Text'),
('smalltalk', 'Smalltalk'),
('sml', 'Standard ML'),
('tcl', 'Tcl'),
('tex', 'TeX'),
('vala', 'Vala'),
('wolfram', 'Wolfram Language');


INSERT IGNORE INTO `databases` (code, display_name) VALUES
('mysql', 'MySQL'),
('mariadb', 'MariaDB'),
('postgresql', 'PostgreSQL'),
('mongodb', 'MongoDB'),
('sqlite', 'SQLite'),
('mssql', 'Microsoft SQL Server'),
('oracle', 'Oracle Database'),
('redis', 'Redis'),
('cassandra', 'Apache Cassandra'),
('dynamodb', 'Amazon DynamoDB'),
('cockroachdb', 'CockroachDB'),
('firebase_realtime_database', 'Firebase Realtime Database'),
('firestore', 'Google Firestore'),
('elasticsearch', 'Elasticsearch'),
('opensearch', 'OpenSearch'),
('neo4j', 'Neo4j'),
('db2', 'IBM Db2'),
('clickhouse', 'ClickHouse'),
('snowflake', 'Snowflake'),
('bigquery', 'Google BigQuery'),
('redshift', 'Amazon Redshift'),
('timescaledb', 'TimescaleDB'),
('influxdb', 'InfluxDB'),
('couchdb', 'CouchDB'),
('couchbase', 'Couchbase'),
('hbase', 'HBase'),
('tidb', 'TiDB'),
('oceanbase', 'OceanBase'),
('greenplum', 'Greenplum'),
('teradata', 'Teradata'),
('vertica', 'Vertica'),
('sap_hana', 'SAP HANA'),
('memcached', 'Memcached'),
('realm', 'Realm'),
('supabase_postgres', 'Supabase Postgres'),
('planet_scale', 'PlanetScale'),
('yugabytedb', 'YugabyteDB'),
('surrealdb', 'SurrealDB'),
('fauna', 'Fauna'),
('rqlite', 'rqlite');

INSERT IGNORE INTO project_categories (code, display_name) VALUES
('ecommerce', 'E-commerce'),
('marketplace', 'Marketplace'),
('subscription_platform', 'Subscription Platform'),
('ticketing_system', 'Ticketing System'),
('booking_system', 'Booking System'),
('on_demand_service', 'On-demand Service'),
('food_delivery', 'Food Delivery'),
('ride_hailing', 'Ride Hailing'),
('pos_system', 'POS System'),
('inventory_management', 'Inventory Management'),
('crm_system', 'CRM System'),
('erp_system', 'ERP System'),
('cms_system', 'CMS System'),
('blog_platform', 'Blog Platform'),
('news_portal', 'News Portal'),
('saas_product', 'SaaS Product'),
('b2b_platform', 'B2B Platform'),
('b2c_platform', 'B2C Platform'),
('c2c_platform', 'C2C Platform'),
('affiliate_system', 'Affiliate System'),
('membership_system', 'Membership System'),
('loyalty_program', 'Loyalty Program'),
('coupon_system', 'Coupon System'),
('payment_gateway_integration', 'Payment Gateway Integration'),
('admin_dashboard', 'Admin Dashboard'),
('data_management_system', 'Data Management System'),
('reporting_dashboard', 'Reporting Dashboard'),
('analytics_platform', 'Analytics Platform'),
('hr_system', 'HR System'),
('attendance_system', 'Attendance System'),
('payroll_system', 'Payroll System'),
('project_management', 'Project Management'),
('task_management', 'Task Management'),
('workflow_system', 'Workflow System'),
('approval_system', 'Approval System'),
('document_management', 'Document Management'),
('knowledge_base', 'Knowledge Base'),
('customer_support_system', 'Customer Support System'),
('helpdesk_system', 'Helpdesk System'),
('chat_system', 'Chat System'),
('internal_tool', 'Internal Tool'),
('education_platform', 'Education Platform'),
('lms_system', 'LMS System'),
('exam_system', 'Exam System'),
('online_course', 'Online Course'),
('dating_platform', 'Dating Platform'),
('social_network', 'Social Network'),
('community_forum', 'Community Forum'),
('event_management', 'Event Management'),
('healthcare_system', 'Healthcare System'),
('clinic_management', 'Clinic Management'),
('hospital_system', 'Hospital System'),
('fitness_platform', 'Fitness Platform'),
('real_estate_platform', 'Real Estate Platform'),
('property_management', 'Property Management'),
('fintech_system', 'Fintech System'),
('insurance_system', 'Insurance System'),
('crypto_platform', 'Crypto Platform'),
('blockchain_application', 'Blockchain Application'),
('iot_system', 'IoT System'),
('smart_device_integration', 'Smart Device Integration'),
('other', 'Other');
