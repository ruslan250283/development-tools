#include <qrencode.h>
#include <qrinput.h>
#include <split.h>
#include <stdio.h>
#include <string.h>


#include <sys/mman.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <unistd.h>
#include <linux/fb.h>
#include <fcntl.h>
#include <stdio.h>
#include <string.h>
#include <stdlib.h>


typedef struct FBINFO{
	struct fb_var_screeninfo var;
	struct fb_fix_screeninfo fix;
	unsigned int fd;
	unsigned int screen_size;
	unsigned int line_width;
	unsigned int pixel_width;
	unsigned char *pmap;
}T_FBINFO,*PT_FBINFO;

typedef struct PixelDatas {
	int iWidth;
	int iHeight;
	int iLineBytes;
	int iTotalBytes;
	unsigned char *aucPixelDatas;
}T_PixelDatas, *PT_PixelDatas;


PT_FBINFO g_ptfbinfo=NULL;



int PicZoom(PT_PixelDatas ptOriginPic, PT_PixelDatas ptZoomPic)
{
    unsigned long dwDstWidth = ptZoomPic->iWidth;
    unsigned long* pdwSrcXTable = malloc(sizeof(unsigned long) * dwDstWidth);
	unsigned long x;
	unsigned long y;
	unsigned long dwSrcY;
	unsigned char *pucDest;
	unsigned char *pucSrc;
//	unsigned long dwPixelBytes = ptOriginPic->iBpp/8;

	
    for (x = 0; x < dwDstWidth; x++)//生成表 pdwSrcXTable
    {
        pdwSrcXTable[x]=(x*ptOriginPic->iWidth/ptZoomPic->iWidth);
    }

    for (y = 0; y < ptZoomPic->iHeight; y++)
    {			
        dwSrcY = (y * ptOriginPic->iHeight / ptZoomPic->iHeight);

		pucDest = ptZoomPic->aucPixelDatas + y*ptZoomPic->iLineBytes;
		pucSrc  = ptOriginPic->aucPixelDatas + dwSrcY*ptOriginPic->iLineBytes;
		
        for (x = 0; x <dwDstWidth; x++)
        {
            /* 原图座标: pdwSrcXTable[x]，srcy
             * 缩放座标: x, y
			 */
			 memcpy(pucDest+x, pucSrc+pdwSrcXTable[x], 1);
        }
    }

    free(pdwSrcXTable);
	return 0;
}


void lcd_pixel_show(int x, int y, unsigned int color)
{
	unsigned char *pen_8 = g_ptfbinfo->pmap+y*g_ptfbinfo->line_width+x*g_ptfbinfo->pixel_width;
	unsigned short *pen_16;	
	unsigned int *pen_32;	

	unsigned int red, green, blue;	

	pen_16 = (unsigned short *)pen_8;
	pen_32 = (unsigned int *)pen_8;

	switch (g_ptfbinfo->var.bits_per_pixel)
	{
		case 8:
		{
			*pen_8 = color;
			break;
		}
		case 16:
		{
			/* 565 */
			red   = (color >> 16) & 0xff;
			green = (color >> 8) & 0xff;
			blue  = (color >> 0) & 0xff;
			color = ((red >> 3) << 11) | ((green >> 2) << 5) | (blue >> 3);
			*pen_16 = color;
			break;
		}
		case 32:
		{
			*pen_32 = color;
			break;
		}
		default:
		{
			printf("can't surport %dbpp\n",g_ptfbinfo->var.bits_per_pixel);
			break;
		}
	}
}
#define ICON_WIDTH 37
int main(int argc, char **argv)
{

int i=0;
int j=0;

QRcode *qrcode;
PT_PixelDatas g_ptOriginPixelDatas=NULL;
PT_PixelDatas g_ptZoomPixelDatas=NULL;
const char *str = "http://www.baidu.com/s?wd=asdada&rsv_spt=1&issp=1&rsv_bp=0&ie=utf-8&tn=baiduhome_pg&f=3&inputT=2780";
QRcode_List *codes ,*list;
int num;

//testStart("Test encode (2-H) (no padding test)");
qrcode = QRcode_encodeString(str, 5, QR_ECLEVEL_L, QR_MODE_8, 1);

printf("version=%d \n",qrcode->version);

//codes = QRcode_encodeStringStructured(str, 1, QR_ECLEVEL_L, QR_MODE_8, 1);

//testEndExp(qrcode->version == 2);
#if 0
i=0;
/*while(codes->code->data[i]!=NULL)
{
	printf("0x%x,",qrcode->data[i]);
		i++;
}*/
list = codes;
	num = 0;
	
while(list != NULL) {
		num++;
		printf("list width is : %d\n",list->code->width);
		i=0;
		while(codes->code->data[i]!=NULL)
		{
			printf("0x%x,",list->code->data[i]);
			i++;
		}
		printf("\ni=%d\n",i);
		list = list->next;
	}
#else
printf("================================\n");
for(i=0;i<ICON_WIDTH;i++)
{
	for(j=0;j<ICON_WIDTH;j++)
	{
		if(qrcode->data[i*ICON_WIDTH+j]&0x01)
		{
			printf("#");
		}
		else
		{
			printf("_");
		}
	}
	printf("\n");
}
printf("\n");
//初始化LCD
	g_ptfbinfo = malloc(sizeof(T_FBINFO));
		if(g_ptfbinfo ==NULL)
		{
	
			printf("[FB]:p_fbinfo malloc failed\n");
			goto err1;
		
		}
		g_ptfbinfo->fd = open("/dev/fb0", O_RDWR);
		if (g_ptfbinfo->fd < 0)
		{
			printf("[FB]:/dev/fb0 can't open\n");
			goto err2;
		}
		if(ioctl(g_ptfbinfo->fd,FBIOGET_VSCREENINFO,&g_ptfbinfo->var))
		{
			printf("[FB]:ioctl val failed \n");
			goto err3;
		}
		if(ioctl(g_ptfbinfo->fd,FBIOGET_FSCREENINFO,&g_ptfbinfo->fix))
		{
			printf("[FB]:ioctl fix failed \n");
			goto err3;
		}	
	
	
		
		g_ptfbinfo->pixel_width = (g_ptfbinfo->var.bits_per_pixel >> 3);
		printf("g_ptfbinfo->pixel_width=%d\n",g_ptfbinfo->pixel_width);
		g_ptfbinfo->line_width = g_ptfbinfo->var.xres * g_ptfbinfo->pixel_width;
		g_ptfbinfo->screen_size = g_ptfbinfo->var.yres * g_ptfbinfo->line_width;
		
		g_ptfbinfo->pmap = (unsigned char *)mmap(0, g_ptfbinfo->screen_size,PROT_READ \
										 | PROT_WRITE,	MAP_SHARED, g_ptfbinfo->fd, 0);
		
		if (g_ptfbinfo->pmap == (unsigned char *)-1)
		{
			printf("[FB]:pmap can't mmap\n");
			goto err3;
		}

		memset(g_ptfbinfo->pmap, 0xffffff,g_ptfbinfo->screen_size );//清屏

	g_ptOriginPixelDatas = malloc(sizeof(T_PixelDatas));
	g_ptZoomPixelDatas   = malloc(sizeof(T_PixelDatas));

	g_ptOriginPixelDatas->iWidth = ICON_WIDTH;
	g_ptOriginPixelDatas->iHeight= ICON_WIDTH;
	g_ptOriginPixelDatas->iLineBytes= g_ptOriginPixelDatas->iWidth;
	g_ptOriginPixelDatas->aucPixelDatas = qrcode->data;
	
	g_ptZoomPixelDatas->iWidth = ICON_WIDTH*1;
	g_ptZoomPixelDatas->iHeight= ICON_WIDTH*1;
	g_ptZoomPixelDatas->iLineBytes= g_ptZoomPixelDatas->iWidth;
	g_ptZoomPixelDatas->aucPixelDatas = malloc(sizeof(char)*ICON_WIDTH*ICON_WIDTH*1*1);

	PicZoom(g_ptOriginPixelDatas, g_ptZoomPixelDatas);
	
	for(i=0;i<g_ptZoomPixelDatas->iWidth;i++)
	{
		for(j=0;j<g_ptZoomPixelDatas->iHeight;j++)
		{
			if(g_ptZoomPixelDatas->aucPixelDatas[i*g_ptZoomPixelDatas->iWidth+j]&0x01)
			{
				lcd_pixel_show(j+10, i+10,0x00);
				//printf("#");
			}
			else
			{
				lcd_pixel_show(j+10, i+10, 0xffffff);
				//printf("_");
			}
		}
		//printf("\n");
		
	}


#if 0
	for(i=0;i<g_ptOriginPixelDatas->iWidth;i++)
	{
		for(j=0;j<g_ptOriginPixelDatas->iHeight;j++)
		{
			if(g_ptOriginPixelDatas->aucPixelDatas[i*g_ptOriginPixelDatas->iWidth+j]&0x01)
			{
				lcd_pixel_show(j+100, i+100,0x00);
				//printf("#");
			}
			else
			{
				lcd_pixel_show(j+100, i+100, 0xffffff);
				//printf("_");
			}
		}
		//printf("\n");
		
	}
		//printf("\n");
#endif
		goto done;
	
err3:
	//close(g_ptfbinfo->fd);
err2:
	//free(g_ptfbinfo);
err1:
	return -1;
done:
	QRcode_free(qrcode);
#endif
	return 0;
}

