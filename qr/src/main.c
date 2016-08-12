#if 1
#include "qrencode.h"
#include <stdio.h>

int main(int argc,char **argv){
    QRcode *qrcode;
    qrcode = QRcode_encodeString("sissi", 2, QR_ECLEVEL_L, QR_MODE_8, 0);
    int i=0;
    int j=0;
    for(i=0;i<25;i++)
    {
        for(j=0;j<25;j++)
        {
            if(qrcode->data[i*25+j]&0x01)
            {
                printf(".");

            }
            else
            {
                printf(" ");

            }
        }
        printf("\n");
    }
printf("\n");
    return 0;
}

#else

#include "qrencode.h"
#include <png.h>
#define INCHES_PER_METER (100.0/2.54)

static void fillRow(unsigned char *row, int size, const unsigned char color[])
{
    int i;

    for(i = 0; i< size; i++) {
        memcpy(row, color, 4);
        row += 4;
    }
}

static int writePNG(const QRcode *qrcode, const char *outfile)
{
    static unsigned char fg_color[4] = {0, 0, 0, 255};
    static unsigned char bg_color[4] = {255, 255, 255, 255};
    static FILE *fp; // avoid clobbering by setjmp.
    png_structp png_ptr;
    png_infop info_ptr;
    png_colorp palette = NULL;
    png_byte alpha_values[2];
    unsigned char *row, *p, *q;
    int x, y, xx, yy, bit;
    int realwidth;
    int margin=4,size=3,dpi=72;

    realwidth = (qrcode->width + margin * 2) * size;

    row = (unsigned char *)malloc(realwidth * 4);

    if(!row)
        return 0;

    if(outfile[0] == '-' && outfile[1] == '\0') {
        fp = stdout;
    } else {
        fp = fopen(outfile, "wb");
        if(!fp)
            return 0;
    }

    png_ptr = png_create_write_struct(PNG_LIBPNG_VER_STRING, NULL, NULL, NULL);
    if(!png_ptr) {
        return 0;
    }

    info_ptr = png_create_info_struct(png_ptr);
    if(!info_ptr) {
        return 0;
    }

    if(setjmp(png_jmpbuf(png_ptr))) {
        png_destroy_write_struct(&png_ptr, &info_ptr);
        return 0;
    }

    png_init_io(png_ptr, fp);

    png_set_IHDR(png_ptr, info_ptr,
        realwidth, realwidth,
        8,
        PNG_COLOR_TYPE_RGB_ALPHA,
        PNG_INTERLACE_NONE,
        PNG_COMPRESSION_TYPE_DEFAULT,
        PNG_FILTER_TYPE_DEFAULT);

    png_set_pHYs(png_ptr, info_ptr,
        dpi * INCHES_PER_METER,
        dpi * INCHES_PER_METER,
        PNG_RESOLUTION_METER);
    png_write_info(png_ptr, info_ptr);


    /* top margin */
    fillRow(row, realwidth, bg_color);
    for(y=0; y<margin * size; y++) {
        png_write_row(png_ptr, row);
    }

    /* data */
    p = qrcode->data;
    for(y=0; y<qrcode->width; y++) {
        fillRow(row, realwidth, bg_color);
        for(x=0; x<qrcode->width; x++) {
            for(xx=0; xx<size; xx++) {
                if(*p & 1) {
                    memcpy(&row[((margin + x) * size + xx) * 4], fg_color, 4);
                }
            }
            p++;
        }
        for(yy=0; yy<size; yy++) {
            png_write_row(png_ptr, row);
        }
    }
    /* bottom margin */
    fillRow(row, realwidth, bg_color);
    for(y=0; y<margin * size; y++) {
        png_write_row(png_ptr, row);
    }

    png_write_end(png_ptr, info_ptr);
    png_destroy_write_struct(&png_ptr, &info_ptr);

    fclose(fp);
    free(row);
    free(palette);

    return 0;
}

inline static int encode(char *utf8_string,char *png_file_path)
{
    QRcode *qrcode=QRcode_encodeString(utf8_string,0,QR_ECLEVEL_H,QR_MODE_8,1);

    if(!qrcode)
        return 0;

    int b=writePNG(qrcode,png_file_path);

    QRcode_free(qrcode);

    return b;
}

int main(int argc,char **argv)
{
    return encode("testUtf8libqrencode","test.png")?0:-1;
}





#endif


