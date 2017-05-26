#!/bin/sh

# バックアップファイルを何日分残しておくか
period=3

# バックアップファイルを保存するディレクトリ
dirpath='/var/www/ranking/backup'

# ファイル名を定義(※ファイル名で日付がわかるようにしておきます)
# PGバックアップ

#filename1=htdocs_backup_`date +%Y%m%d`
#tar cfz $dirpath/$filename1.tar.gz /var/www/kaikei/public/

#filename2=application_backup_`date +%Y%m%d`
#tar cfz $dirpath/$filename2.tar.gz /home/fnote/www/fnote.com.dev/application/

#filename3=kaikei_backup_`date +%Y%m%d`
#filename4=projects_backup_`date +%Y%m%d`
filename5=ranking_backup_`date +%Y%m%d`

#tar cfz $dirpath/$filename3.tar.gz /var/www/kaikei/modules/
#tar cfz $dirpath/$filename4.tar.gz /var/www/projects/modules/
tar cfz $dirpath/$filename5.tar.gz /var/www/ranking/modules/



# システム全体
#filename6=system_backup_`date +%Y%m%d`
#tar cfz $dirpath/$filename6.tar.gz /home/fnote/www/fnote.com.dev/



# パーミッション変更
#chmod 754 $dirpath/$filename1.tar.gz
#chmod 754 $dirpath/$filename2.tar.gz
#chmod 754 $dirpath/$filename3.tar.gz
#chmod 754 $dirpath/$filename4.tar.gz
chmod 754 $dirpath/$filename5.tar.gz
#chmod 754 $dirpath/$filename6.tar.gz

# 古いバックアップファイルを削除
#oldfile=htdocs_backup_`date --date "$period days ago" +%Y%m%d`
#rm -f $dirpath/$oldfile.tar.gz
#oldfile=kaikei_backup_`date --date "$period days ago" +%Y%m%d`
#rm -f $dirpath/$oldfile.tar.gz
#oldfile=projects_backup_`date --date "$period days ago" +%Y%m%d`
#rm -f $dirpath/$oldfile.tar.gz
oldfile=ranking_backup_`date --date "$period days ago" +%Y%m%d`
rm -f $dirpath/$oldfile.tar.gz
#oldfile=system_backup_`date --date "$period days ago" +%Y%m%d`
#rm -f $dirpath/$oldfile.tar.gz
