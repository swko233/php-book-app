FROM mysql:5.7

# 日本語化の設定ファイルをマウント
# MySQLの公式イメージには /etc/mysql/conf.d/ 以下に設定ファイルを配置すると自動で読み込む機構があるため、ファイルを設置するだけで良い
ADD ./Docker/mysql/conf.d/my.cnf /etc/mysql/conf.d/my.cnf