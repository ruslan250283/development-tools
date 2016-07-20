#!/bin/bash
#create by luwl


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
		if [ $( echo \"$l_name\"|sed 's#[/]*$##') == "\"default\"" ]; then
	    	continue 1
		fi
		if [ $( echo \"$l_name\"|sed 's#[/]*$##') == "\"tools\"" ]; then
	    	continue 1
		fi
		[[ $( echo \"$l_name\"|sed 's#[/]*$##') =~ "65C" ]] && continue 1
		l_name_ret="$l_name_ret$l_name "
    done

    echo "$l_name_ret"
}

echo "1. user"
echo "2. eng"
echo "3. userdebug"

read -p "" debug_version_select

l_menu_name=0
l_menu_id=0
l_into_dir="../customfiles"
l_projects_name=($(get_dir_name_in_this_dir "$l_into_dir"))
echo "$l_projects_name"
for l_menu_name in ${l_projects_name[@]}
do
    echo -e "\t$l_menu_id.$l_menu_name"
  	    let l_menu_id=l_menu_id+1
done

read l_select_id

PRJ_NAME=${l_projects_name[$l_select_id]}

echo "$PRJ_NAME"

##add by wzb for ngm plus
if [ "$PRJ_NAME" = "6735M_65U_V2_FWVGA_NGMPLUS_H3G" ] ; then
echo "plus"
rm -rf $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/alps $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/phone_ext $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/poweronoff $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/wallpaper
cp -rf $l_into_dir/6735M_65U_V2_FWVGA_NGM_H3G/* $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G
cp -rf $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/plusalps/* $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/alps
cp -rf $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/audio_ver1_volume_custom_default.h $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/phone_ext/audio_volume/
cp -rf $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/audio_acf_default.h $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/alps/vendor/mediatek/proprietary/custom/common/cgen/cfgdefault/
fi

if [ "$PRJ_NAME" = "6735M_65U_V2_FWVGA_NGMPLUS_H3G_SS" ] ; then
echo "plus ss"
rm -rf $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G_SS/*
rm -rf $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/alps $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/phone_ext $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/poweronoff $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/wallpaper
cp -rf $l_into_dir/6735M_65U_V2_FWVGA_NGM_H3G/* $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G
cp -rf $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/plusalps/* $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/alps
cp -rf $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/audio_ver1_volume_custom_default.h $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/phone_ext/audio_volume/
cp -rf $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/audio_acf_default.h $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/alps/vendor/mediatek/proprietary/custom/common/cgen/cfgdefault/
cp -rf $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/* $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G_SS/
fi

if [ "$PRJ_NAME" = "6735M_65U_V2_FWVGA_NGMPLUS_H3G_NOGPS" ] ; then
echo "no gps plus"
rm -rf $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G_NOGPS/alps $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G_NOGPS/phone_ext $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G_NOGPS/poweronoff $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G_NOGPS/wallpaper
cp -rf $l_into_dir/6735M_65U_V2_FWVGA_NGM_H3G/* $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G_NOGPS
cp -rf $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G_NOGPS/nogpsalps/* $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G_NOGPS/alps
cp -rf $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G_NOGPS/audio_ver1_volume_custom_default.h $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G_NOGPS/phone_ext/audio_volume/
cp -rf $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G_NOGPS/audio_acf_default.h $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G_NOGPS/alps/vendor/mediatek/proprietary/custom/common/cgen/cfgdefault/
cp -rf $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G_NOGPS/*.bmp $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G_NOGPS/poweronoff/logo/fwvga/
fi

if [ "$PRJ_NAME" = "6735M_65U_V2_FWVGA_NGM_H3G_SS" ] ; then
echo "ngm ss"
rm -rf $l_into_dir/6735M_65U_V2_FWVGA_NGM_H3G_SS/*
cp -rf $l_into_dir/6735M_65U_V2_FWVGA_NGM_H3G/* $l_into_dir/6735M_65U_V2_FWVGA_NGM_H3G_SS/
fi

if [ "$PRJ_NAME" = "6735M_65U_V2_FWVGA_PCD_NI" ] ; then
echo "ni"
rm -rf $l_into_dir/6735M_65U_V2_FWVGA_PCD_NI/alps $l_into_dir/6735M_65U_V2_FWVGA_PCD_NI/phone_ext $l_into_dir/6735M_65U_V2_FWVGA_PCD_NI/poweronoff $l_into_dir/6735M_65U_V2_FWVGA_PCD_NI/wallpaper
cp -rf $l_into_dir/6735M_65U_V2_FWVGA_PCD/* $l_into_dir/6735M_65U_V2_FWVGA_PCD_NI
cp -rf $l_into_dir/6735M_65U_V2_FWVGA_PCD_NI/nialps/* $l_into_dir/6735M_65U_V2_FWVGA_PCD_NI/alps
#cp -rf $l_into_dir/6735M_65U_V2_FWVGA_PCD.mk $l_into_dir/6735M_65U_V2_FWVGA_PCD_NI.mk
#sed -i 's/CLARO_PCD_PL5001_CA/CLARO_PCD_PL5001_NI/g' $l_into_dir/6735M_65U_V2_FWVGA_PCD_NI.mk
#sed -i 's/Claro_GT/Claro_NI/g' $l_into_dir/6735M_65U_V2_FWVGA_PCD_NI.mk
fi

if [ "$PRJ_NAME" = "6735M_65U_V2_FWVGA_PCD_HN" ] ; then
echo "hn"
rm -rf $l_into_dir/6735M_65U_V2_FWVGA_PCD_HN/alps $l_into_dir/6735M_65U_V2_FWVGA_PCD_HN/phone_ext $l_into_dir/6735M_65U_V2_FWVGA_PCD_HN/poweronoff $l_into_dir/6735M_65U_V2_FWVGA_PCD_HN/wallpaper
cp -rf $l_into_dir/6735M_65U_V2_FWVGA_PCD/* $l_into_dir/6735M_65U_V2_FWVGA_PCD_HN
cp -rf $l_into_dir/6735M_65U_V2_FWVGA_PCD_HN/hnalps/* $l_into_dir/6735M_65U_V2_FWVGA_PCD_HN/alps
#cp -rf $l_into_dir/6735M_65U_V2_FWVGA_PCD.mk $l_into_dir/6735M_65U_V2_FWVGA_PCD_HN.mk
#sed -i 's/CLARO_PCD_PL5001_CA/CLARO_PCD_PL5001_HN/g' $l_into_dir/6735M_65U_V2_FWVGA_PCD_HN.mk
#sed -i 's/Claro_GT/Claro_HN/g' $l_into_dir/6735M_65U_V2_FWVGA_PCD_HN.mk
fi

if [ "$PRJ_NAME" = "6735M_65U_V2_FWVGA_PCD_SV" ] ; then
echo "sv"
rm -rf $l_into_dir/6735M_65U_V2_FWVGA_PCD_SV/alps $l_into_dir/6735M_65U_V2_FWVGA_PCD_SV/phone_ext $l_into_dir/6735M_65U_V2_FWVGA_PCD_SV/poweronoff $l_into_dir/6735M_65U_V2_FWVGA_PCD_SV/wallpaper
cp -rf $l_into_dir/6735M_65U_V2_FWVGA_PCD/* $l_into_dir/6735M_65U_V2_FWVGA_PCD_SV
cp -rf $l_into_dir/6735M_65U_V2_FWVGA_PCD_SV/svalps/* $l_into_dir/6735M_65U_V2_FWVGA_PCD_SV/alps
#cp -rf $l_into_dir/6735M_65U_V2_FWVGA_PCD.mk $l_into_dir/6735M_65U_V2_FWVGA_PCD_SV.mk
#sed -i 's/CLARO_PCD_PL5001_CA/CLARO_PCD_PL5001_SV/g' $l_into_dir/6735M_65U_V2_FWVGA_PCD_SV.mk
#sed -i 's/Claro_GT/Claro_SV/g' $l_into_dir/6735M_65U_V2_FWVGA_PCD_SV.mk
fi

##end

source build/envsetup.sh
source mbldenv.sh

echo "${debug_version_select}"
case "$debug_version_select" in
        1)
        lunch full_huay6735m_65u_b_l1-user
        ;;
        2)
        lunch full_huay6735m_65u_b_l1-eng
        ;;
        3)
        lunch full_huay6735m_65u_b_l1-userdebug
        ;;
        *)
       echo "default user mode."
        lunch full_huay6735m_65u_b_l1-user

esac


PRJ_TYPE=huay6735m_65u_b_l1
echo "hxbuild.pl $PRJ_TYPE $PRJ_NAME"
perl  hxbuild.pl $PRJ_TYPE $PRJ_NAME

if [ "$PRJ_NAME" = "6735M_65U_V2_FWVGA_NGMPLUS_H3G" ] ; then
rm -rf $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/alps $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/phone_ext $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/poweronoff $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/wallpaper
fi

if [ "$PRJ_NAME" = "6735M_65U_V2_FWVGA_NGMPLUS_H3G_SS" ] ; then
rm -rf $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/alps $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/phone_ext $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/poweronoff $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G/wallpaper
rm -rf $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G_SS/*
fi

if [ "$PRJ_NAME" = "6735M_65U_V2_FWVGA_NGMPLUS_H3G_NOGPS" ] ; then
rm -rf $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G_NOGPS/alps $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G_NOGPS/phone_ext $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G_NOGPS/poweronoff $l_into_dir/6735M_65U_V2_FWVGA_NGMPLUS_H3G_NOGPS/wallpaper
fi

if [ "$PRJ_NAME" = "6735M_65U_V2_FWVGA_NGM_H3G_SS" ] ; then
rm -rf $l_into_dir/6735M_65U_V2_FWVGA_NGM_H3G_SS/*
fi

if [ "$PRJ_NAME" = "6735M_65U_V2_FWVGA_PCD_NI" ] ; then
rm -rf $l_into_dir/6735M_65U_V2_FWVGA_PCD_NI/alps $l_into_dir/6735M_65U_V2_FWVGA_PCD_NI/phone_ext $l_into_dir/6735M_65U_V2_FWVGA_PCD_NI/poweronoff $l_into_dir/6735M_65U_V2_FWVGA_PCD_NI/wallpaper
#rm -rf $l_into_dir/6735M_65U_V2_FWVGA_PCD_NI.mk
fi

if [ "$PRJ_NAME" = "6735M_65U_V2_FWVGA_PCD_HN" ] ; then
rm -rf $l_into_dir/6735M_65U_V2_FWVGA_PCD_HN/alps $l_into_dir/6735M_65U_V2_FWVGA_PCD_HN/phone_ext $l_into_dir/6735M_65U_V2_FWVGA_PCD_HN/poweronoff $l_into_dir/6735M_65U_V2_FWVGA_PCD_HN/wallpaper
#rm -rf $l_into_dir/6735M_65U_V2_FWVGA_PCD_HN.mk
fi

if [ "$PRJ_NAME" = "6735M_65U_V2_FWVGA_PCD_SV" ] ; then
rm -rf $l_into_dir/6735M_65U_V2_FWVGA_PCD_SV/alps $l_into_dir/6735M_65U_V2_FWVGA_PCD_SV/phone_ext $l_into_dir/6735M_65U_V2_FWVGA_PCD_SV/poweronoff $l_into_dir/6735M_65U_V2_FWVGA_PCD_SV/wallpaper
#rm -rf $l_into_dir/6735M_65U_V2_FWVGA_PCD_SV.mk
fi
