#!/bin/bash
cp /data/wzb/code/mt6735_5.1/2_mt6735/mt6735/alps/out/target/product/huay6735m_65u_b_l1/target_files-package.zip ./
cp /data/wzb/code/mt6735_5.1/2_mt6735/mt6735/alps/out/target/product/huay6735m_65u_b_l1/ota_scatter.txt ./
unzip target_files-package.zip
shopt -s extglob
rm -rf !(copy_fota.sh|ota_target_files.zip|ota_scatter.txt)
shopt -u extglob
OTA_NAME=$(basename $PWD)
echo $OTA_NAME
mv ota_target_files.zip $OTA_NAME.zip