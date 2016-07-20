# /* __NH _1205 _17__ */

hide         :=  @

ifeq ($(PRJ_MAK_FILE),)
  $(error PRJ_MAK_FILE is not defined)
endif

include $(PRJ_MAK_FILE)


# mediatek\build\libs\custom.mk
include device/mediatek/build/build/libs/gmsl

define .if-cfg-on
$(if $(filter-out NO NONE FALSE,$(call uc,$(strip $($(1))))),$(2),$(3))
endef
define mtk.custom.generate-macros
$(strip $(foreach t,$(JXPP_AUTO_ADD_GLOBAL_DEFINE_BY_NAME),$(call .if-cfg-on,$(t),-D$(call uc,$(t))))
$(foreach t,$(JXPP_AUTO_ADD_GLOBAL_DEFINE_BY_VALUE),$(call .if-cfg-on,$(t),$(foreach v, $(call uc,$($(t))),-D$(v))))
$(foreach t,$(JXPP_AUTO_ADD_GLOBAL_DEFINE_BY_NAME_VALUE),$(call .if-cfg-on,$(t),-D$(call uc,$(t))=$(strip $($(t))))))
endef
# /* __NH _1205 _23__ */ ""
#$(foreach t,$(AUTO_ADD_GLOBAL_DEFINE_BY_NAME_VALUE),$(call .if-cfg-on,$(t),-D$(call uc,$(t))=\\\"$(strip $($(t)))\\\")))

PRJ_DEF_FILE   := ../customfiles/tools/jxpp/prj_def.txt
PRJ_UNDEF_FILE := ../customfiles/tools/jxpp/prj_undef.txt

.PHONY : all

all:
	$(hide) rm -f $(strip $(PRJ_DEF_FILE))
	$(hide) rm -f $(strip $(PRJ_UNDEF_FILE))

	
#	$(hide) echo $(foreach def,$(PRJ_DEFS),-D$(def)) > $(strip $(PRJ_DEF_FILE))
#	$(hide) echo $(foreach def,$(PRJ_UNDEFS),-U$(def)) > $(strip $(PRJ_UNDEF_FILE))
	$(hide) echo $(call mtk.custom.generate-macros) > $(strip $(PRJ_DEF_FILE))
	$(hide) echo   > $(strip $(PRJ_UNDEF_FILE))
	
