import zipfile, csv, io, time, os, sys, mysql.connector

BASE_DIR = r'C:\xampp\htdocs\crm'
DATA_DIR = os.path.join(BASE_DIR, 'data')
BATCH_SIZE = 2000

DB_CONFIG = {'host':'127.0.0.1','user':'root','password':'','database':'crm_cnpj'}

print('=== IMPORTADOR CNPJ - MATO GROSSO DO SUL ===')
print('Conectando ao banco...')
conn = mysql.connector.connect(**DB_CONFIG)
cursor = conn.cursor()

# ======== 1. TABELA PRINCIPAL ========
print('\n[1/4] Criando tabela principal estabelecimentos_ms...')
cursor.execute("DROP TABLE IF EXISTS estabelecimentos_ms")
cursor.execute("""
CREATE TABLE estabelecimentos_ms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cnpj_basico VARCHAR(8),
    cnpj_ordem VARCHAR(4),
    cnpj_dv VARCHAR(2),
    identificador_matriz_filial VARCHAR(1),
    nome_fantasia TEXT,
    razao_social TEXT,
    situacao_cadastral VARCHAR(2),
    data_situacao_cadastral VARCHAR(8),
    data_inicio_atividade VARCHAR(8),
    cnae_fiscal_principal VARCHAR(7),
    tipo_logradouro VARCHAR(50),
    logradouro TEXT,
    numero VARCHAR(20),
    complemento TEXT,
    bairro TEXT,
    cep VARCHAR(8),
    uf VARCHAR(2),
    municipio VARCHAR(4),
    ddd VARCHAR(3),
    telefone VARCHAR(10),
    correio_eletronico TEXT,
    capital_social VARCHAR(20),
    natureza_juridica VARCHAR(4),
    porte VARCHAR(2),
    INDEX idx_cnpj (cnpj_basico),
    INDEX idx_municipio (municipio),
    INDEX idx_situacao (situacao_cadastral),
    INDEX idx_cnae (cnae_fiscal_principal),
    INDEX idx_email (correio_eletronico(50))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
""")
conn.commit()

# ======== 2. IMPORTAR EMPRESAS ========
print('\n[2/4] Importando Empresas (razao_social, capital)...')
cursor.execute("DROP TABLE IF EXISTS empresas_tmp")
cursor.execute("""
CREATE TABLE empresas_tmp (
    cnpj_basico VARCHAR(8) PRIMARY KEY,
    razao_social TEXT,
    natureza_juridica VARCHAR(4),
    capital_social VARCHAR(20),
    porte VARCHAR(2),
    INDEX idx_cnpj (cnpj_basico)
) ENGINE=InnoDB
""")
conn.commit()

sql_emp = "INSERT INTO empresas_tmp (cnpj_basico, razao_social, natureza_juridica, capital_social, porte) VALUES (%s,%s,%s,%s,%s)"
total_emp = 0
for i in range(10):
    fname = f'Empresas{i}.zip'
    fpath = os.path.join(DATA_DIR, fname)
    if not os.path.exists(fpath): continue
    z = zipfile.ZipFile(fpath)
    f = z.open(z.namelist()[0])
    reader = csv.reader(io.TextIOWrapper(f, encoding='latin-1'), delimiter=';', quotechar='"')
    batch = []
    for row in reader:
        if len(row) < 5: continue
        batch.append((row[0].strip('"'), row[1].strip('"'), row[2].strip('"'), row[4].strip('"'), row[5].strip('"')))
        if len(batch) >= BATCH_SIZE:
            cursor.executemany(sql_emp, batch)
            conn.commit(); total_emp += len(batch); batch = []
    if batch:
        cursor.executemany(sql_emp, batch)
        conn.commit(); total_emp += len(batch)
    f.close(); z.close()
    print(f'  {fname}: OK ({total_emp} total)')

# ======== 3. IMPORTAR ESTABELECIMENTOS MS ========
print(f'\n[3/4] Importando Estabelecimentos (filtrando MS)...')
sql_est = """INSERT INTO estabelecimentos_ms 
    (cnpj_basico, cnpj_ordem, cnpj_dv, identificador_matriz_filial,
     nome_fantasia, situacao_cadastral, data_situacao_cadastral, data_inicio_atividade,
     cnae_fiscal_principal, tipo_logradouro, logradouro, numero, complemento,
     bairro, cep, uf, municipio, ddd, telefone, correio_eletronico,
     capital_social, natureza_juridica, porte)
    VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"""

total_est = 0
total_lines = 0
start_all = time.time()

for i in range(10):
    fname = f'Estabelecimentos{i}.zip'
    fpath = os.path.join(DATA_DIR, fname)
    if not os.path.exists(fpath): continue
    
    z = zipfile.ZipFile(fpath)
    f = z.open(z.namelist()[0])
    reader = csv.reader(io.TextIOWrapper(f, encoding='latin-1'), delimiter=';', quotechar='"')
    
    file_ms = 0
    batch = []
    t0 = time.time()
    
    for row in reader:
        total_lines += 1
        if len(row) < 20 or row[19].strip('"') != 'MS':
            continue
        
        file_ms += 1
        cnpj = row[0].strip('"')
        
        # Get empresa data
        cursor.execute("SELECT razao_social, natureza_juridica, capital_social, porte FROM empresas_tmp WHERE cnpj_basico = %s", (cnpj,))
        emp = cursor.fetchone()
        
        batch.append((
            cnpj,
            row[1].strip('"'),  # ordem
            row[2].strip('"'),  # dv
            row[3].strip('"'),  # matriz
            row[4].strip('"'),  # fantasia
            row[5].strip('"'),  # situacao
            row[6].strip('"'),  # data_situacao
            row[10].strip('"'), # data_inicio
            row[11].strip('"'), # cnae
            row[13].strip('"'), # tipo_logradouro
            row[14].strip('"'), # logradouro
            row[15].strip('"'), # numero
            row[16].strip('"'), # complemento
            row[17].strip('"'), # bairro
            row[18].strip('"'), # cep
            row[19].strip('"'), # uf
            row[20].strip('"'), # municipio
            row[21].strip('"'), # ddd
            row[22].strip('"'), # telefone
            row[27].strip('"'), # email
            emp[2] if emp else '',  # capital_social
            emp[1] if emp else '',  # natureza_juridica
            emp[3] if emp else '',  # porte
        ))
        
        if len(batch) >= BATCH_SIZE:
            cursor.executemany(sql_est, batch)
            conn.commit()
            total_est += len(batch)
            batch = []
            
            elapsed = time.time() - t0
            rate = file_ms / elapsed if elapsed > 0 else 0
            pct = (i * 100) / 10
            print(f'  [{pct:.0f}%] File {i}: {file_ms} MS records ({rate:.0f}/s)', end='\r')
    
    if batch:
        cursor.executemany(sql_est, batch)
        conn.commit()
        total_est += len(batch)
    
    elapsed = time.time() - t0
    f.close(); z.close()
    print(f'  File {i}: {file_ms} MS records in {elapsed:.0f}s')

# ======== 4. CRIAR TABELAS POR SEGMENTO ========
print(f'\n\n[4/4] Criando tabelas por segmento (todas as cidades MS)...')

# CNAE segments (same as original)
cnae_segments = {
    'arquitetos': ('7111100', '7111100'),
    'designers_interiores': ('7410202', '7410202'),
    'designers': ('7410201', '7410203'),
    'construtoras': ('4120400', '4120400'),
    'imobiliarias': ('6821801', '6821801'),
    'engenheiros': ('7112000', '7112000'),
    'lojas_marcenarias': ('3101200', '3104700'),
    'paisagismo_decoracao': ('8130300', '8130300'),
}

# Get city list from imported data
cursor.execute("SELECT DISTINCT municipio FROM estabelecimentos_ms ORDER BY municipio")
cities = [r[0] for r in cursor.fetchall()]

# Update municipios UF
for mun in cities:
    cursor.execute("UPDATE municipios SET uf = 'MS' WHERE codigo = %s", (mun,))
conn.commit()

# Get city names
cursor.execute("SELECT codigo, nome FROM municipios WHERE codigo IN ({})".format(
    ','.join(f"'{m}'" for m in cities)))
city_names = {r[0]: r[1] for r in cursor.fetchall()}

# Create state-level segment tables
for seg_name, (cnae_start, cnae_end) in cnae_segments.items():
    table_name = f'{seg_name}_ms'
    cursor.execute(f"DROP TABLE IF EXISTS `{table_name}`")
    cursor.execute(f"""
        CREATE TABLE `{table_name}` AS
        SELECT e.cnpj_basico, e.razao_social, e.nome_fantasia,
               e.cnae_fiscal_principal, e.tipo_logradouro, e.logradouro,
               e.numero, e.complemento, e.bairro, e.cep, e.uf, e.municipio,
               e.ddd, e.telefone, e.correio_eletronico,
               e.data_inicio_atividade, e.situacao_cadastral,
               e.capital_social, e.natureza_juridica, e.porte,
               COALESCE(m.nome, e.municipio) as nome_municipio
        FROM estabelecimentos_ms e
        LEFT JOIN municipios m ON m.codigo = e.municipio
        WHERE e.situacao_cadastral = '02'
          AND e.cnae_fiscal_principal >= '{cnae_start}'
          AND e.cnae_fiscal_principal <= '{cnae_end}'
    """)
    cursor.execute(f"ALTER TABLE `{table_name}` ADD INDEX idx_email (correio_eletronico(50))")
    cursor.execute(f"ALTER TABLE `{table_name}` ADD INDEX idx_municipio (municipio)")
    cursor.execute(f"ALTER TABLE `{table_name}` ADD INDEX idx_cnpj (cnpj_basico)")
    
    r = cursor.execute(f"SELECT COUNT(*) FROM `{table_name}`")
    count = cursor.fetchone()[0]
    print(f'  {seg_name}_ms: {count} registros')

# Create views per city for backward compatibility
print(f'\n  Criando views por cidade...')
for seg_name in cnae_segments:
    for mun in cities:
        view_name = f'{seg_name}_{mun}'
        cursor.execute(f"DROP TABLE IF EXISTS `{view_name}`")
        cursor.execute(f"""
            CREATE TABLE `{view_name}` AS
            SELECT * FROM `{seg_name}_ms` WHERE municipio = '{mun}'
        """)
        cursor.execute(f"ALTER TABLE `{view_name}` ADD INDEX idx_email (correio_eletronico(50))")

conn.commit()
cursor.close()
conn.close()

# Summary
print(f'\n=== RESUMO ===')
print(f'Cidades: {len(cities)}')
for mun in cities:
    name = city_names.get(mun, mun)
    cursor2 = mysql.connector.connect(**DB_CONFIG)
    c = cursor2.cursor()
    c.execute(f"SELECT COUNT(*) FROM estabelecimentos_ms WHERE municipio='{mun}' AND situacao_cadastral='02'")
    total = c.fetchone()[0]
    c.close(); cursor2.close()
    print(f'  {name} ({mun}): {total} estabelecimentos ativos')

elapsed_total = time.time() - start_all
print(f'\n=== FINALIZADO em {elapsed_total/60:.1f} minutos ===')
print(f'Total de registros MS: {total_est}')
print(f'Total de linhas processadas: {total_lines}')
print(f'Velocidade media: {total_lines/elapsed_total:.0f} linhas/s')
