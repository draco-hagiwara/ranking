#!/bin/sh

# バックアップファイルを何日分残しておくか
period=3

# バックアップファイルを保存するディレクトリ
dirpath='/var/www/kaikei/backup'

# ファイル名を定義(※ファイル名で日付がわかるようにしておきます)
#filename1=db_mysqldump_`date +%Y%m%d-%H%M%S`

# 手動バックアップを判定
if [ $1 = 1 ]; then
  filename2=invoice_mysqldump_`date +%Y%m%d%H%M%S`
  filename3=seorank_mysqldump_`date +%Y%m%d%H%M%S`
  filename4=projects_mysqldump_`date +%Y%m%d%H%M%S`
else
  filename2=invoice_mysqldump_`date +%Y%m%d`
  filename3=seorank_mysqldump_`date +%Y%m%d`
  filename4=projects_mysqldump_`date +%Y%m%d`
fi


# mysqldump実行（ファイルサイズ圧縮の為gzで圧縮しておきます。）
#/opt/lampp/bin/mysqldump --opt --all-databases --events --default-character-set=binary -u root --password=db!mp | gzip > $dirpath/$filename1.sql.gz
#mysqldump --opt --all-databases --events --default-character-set=binary --u root --password=db!mp | gzip > $dirpath/$filename1.sql.gz
/opt/lampp/bin/mysqldump invoice -u invoice --password=db!mp | gzip > $dirpath/$filename2.sql.gz
/opt/lampp/bin/mysqldump seorank -u seorank  --password=db!mp | gzip > $dirpath/$filename3.sql.gz
/opt/lampp/bin/mysqldump projects -u projects  --password=db!mp | gzip > $dirpath/$filename4.sql.gz



# パーミッション変更
#chmod 754 $dirpath/$filename1.sql.gz
chmod 754 $dirpath/$filename2.sql.gz
chmod 754 $dirpath/$filename3.sql.gz
chmod 754 $dirpath/$filename4.sql.gz

# 古いバックアップファイルを削除
#oldfile=db_mysqldump_`date --date "$period days ago" +%Y%m%d-%H%M%S`
#rm -f $dirpath/$oldfile.sql.gz
oldfile=invoice_mysqldump_`date --date "$period days ago" +%Y%m%d`
rm -f $dirpath/$oldfile.sql.gz
oldfile=seorank_mysqldump_`date --date "$period days ago" +%Y%m%d`
rm -f $dirpath/$oldfile.sql.gz
oldfile=projects_mysqldump_`date --date "$period days ago" +%Y%m%d`
rm -f $dirpath/$oldfile.sql.gz


## --リストアコマンド
## gunzip mybackup_xxxxxx.sql.gz
## mysql -u root -p < mybackup_xxxxxx.sql
## /opt/lampp/bin/mysql invoice -u root -p  < invoice_mysqldump_xxxxxx.sql
## /opt/lampp/bin/mysql seorank -u root -p  < seorank_mysqldump_xxxxxx.sql
## /opt/lampp/bin/mysql projects -u root -p  < projects_mysqldump_xxxxxx.sql
