#!/bin/bash
# ##########################################################
# ALPS(Android4.1 based) build environment profile setting
# ##########################################################
# Overwrite JAVA_HOME environment variable setting if already exists
echo -e '\033[0;31;1m'
echo "******************"
echo "*********MOTA*********"
echo "******************"
echo -e '\033[0m'

source ./../build/envsetup.sh
source ./../mbldenv.sh

function get_file_name()
{
    local l_file_name=`echo "$1"|sed -n 's#.*/\([^/][^/]*[/]*\)$#\1#p'` 
    echo "$l_file_name"
}

function get_dir_name_in_this_dir()
{
    local l_this_dir_path=""
    local l_dirs_name=""
    local l_name_ret=""
    local l_name=""
    local tmp=""
 
    if [ "$#" -eq 0 ]; then
		echo "!!error!! you must get one parameter">>"$L_HANDER_LOG_FILE"
		return 1
    fi
 
    l_this_dir_path="$1"
 
    if ! [ -d "$l_this_dir_path" ]; then
		echo "!!error!! $l_this_dir_path is't not exist" #>>"$L_HANDER_LOG_FILE"
		return 1
    fi
    l_dirs_name=$(find "$l_this_dir_path" -maxdepth 1 -type d)
    l_dirs_name=$(echo "$l_dirs_name"|sort |sed 'N;s#\n# #g')

    for l_name in $l_dirs_name
    do
		l_name=$(get_file_name "$l_name")
	 	tmp="$(get_file_name "$l_this_dir_path")"

    #echo "1=$( echo \"$l_name\"|sed 's#[/]*$##')"    
		if [ $( echo \"$l_name\"|sed 's#[/]*$##') == $(echo \"$tmp\"|sed 's#[/]*$##') ]; then 
	    	continue 1
		fi
		if [ $( echo \"$l_name\"|sed 's#[/]*$##') == "\"out\"" ]; then 
	    	continue 1
		fi
		
		l_name_ret="$l_name_ret$l_name "
    done

    echo "$l_name_ret" 
}

l_menu_name=0
l_menu_id=0
l_into_dir="./../bin/mota"
l_projects_name=($(get_dir_name_in_this_dir "$l_into_dir"))
#echo "$l_projects_name"
for l_menu_name in ${l_projects_name[@]}
do
    echo -e "\t$l_menu_id.$l_menu_name"
  	    let l_menu_id=l_menu_id+1
done
echo -e '\033[0;31;1m'
echo "************************************"
echo "please select project!"
echo "************************************"
echo -e '\033[0m'
read l_select_id
PRJ_NAME=${l_projects_name[$l_select_id]}
echo -e '\033[0;31;1m'
echo "the project is $PRJ_NAME"
echo -e '\033[0m'

l_menu_id=0
l_projects_name=($(get_dir_name_in_this_dir "$l_into_dir/$PRJ_NAME"))
for l_menu_name in ${l_projects_name[@]}
do
    echo -e "\t$l_menu_id.$l_menu_name"
  	    let l_menu_id=l_menu_id+1
done
echo -e '\033[0;31;1m'
echo "************************************"
echo "please select old version!"
echo "************************************"
echo -e '\033[0m'
read l_select_id
#echo $l_select_id
OLD_VER_NAME=${l_projects_name[$l_select_id]}
echo -e '\033[0;31;1m'
echo "old version is $OLD_VER_NAME"
echo -e '\033[0m'

echo -e '\033[0;31;1m'
echo "************************************"
echo "please select new version!"
echo "************************************"
echo -e '\033[0m'
read l_select_id
#echo $l_select_id
NEW_VER_NAME=${l_projects_name[$l_select_id]}
echo -e '\033[0;31;1m'
echo "new version is $NEW_VER_NAME"
echo -e '\033[0m'

##for ota_scatter.txt
mv ./out/target/product/huay6735m_65u_b_l1/ota_scatter.txt ./out/target/product/huay6735m_65u_b_l1/bak.txt
mv ./out/target/product/huay6735m_65c_b_l1/ota_scatter.txt ./out/target/product/huay6735m_65c_b_l1/bak.txt
cp ./../bin/mota/$PRJ_NAME/$OLD_VER_NAME/ota_scatter.txt ./out/target/product/huay6735m_65u_b_l1/
cp ./../bin/mota/$PRJ_NAME/$OLD_VER_NAME/ota_scatter.txt ./out/target/product/huay6735m_65c_b_l1/

echo "./build/tools/releasetools/ota_from_target_files -i ./../bin/mota/$PRJ_NAME/$OLD_VER_NAME/$OLD_VER_NAME.zip  ./../bin/mota/$PRJ_NAME/$NEW_VER_NAME/$NEW_VER_NAME.zip update.zip"
./build/tools/releasetools/ota_from_target_files -i ./../bin/mota/$PRJ_NAME/$OLD_VER_NAME/$OLD_VER_NAME.zip  ./../bin/mota/$PRJ_NAME/$NEW_VER_NAME/$NEW_VER_NAME.zip update.zip
#./build/tools/releasetools/ota_from_target_files -u ./../bin/mota/$PRJ_NAME/$NEW_VER_NAME/lk.bin -i ./../bin/mota/$PRJ_NAME/$OLD_VER_NAME/$OLD_VER_NAME.zip  ./../bin/mota/$PRJ_NAME/$NEW_VER_NAME/$NEW_VER_NAME.zip update.zip

DELTA_NAME="$OLD_VER_NAME--$NEW_VER_NAME"
mkdir ./../bin/mota/out/$DELTA_NAME
mv update.zip ./../bin/mota/out/$DELTA_NAME/
cd ./../bin/mota/out/$DELTA_NAME/
md5sum -b update.zip > md5sum
zip package.zip update.zip md5sum
cd -
mv ./out/target/product/huay6735m_65u_b_l1/bak.txt ./out/target/product/huay6735m_65u_b_l1/ota_scatter.txt
mv ./out/target/product/huay6735m_65c_b_l1/bak.txt ./out/target/product/huay6735m_65c_b_l1/ota_scatter.txt





