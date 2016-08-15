

CFLAGS 	:= -Wall -O2 -c -g
CFLAGS  += -I$(PWD)

LDFLAGS := -lm

CC = gcc-4.6
LD = gcc-4.6

OBJS := main.o                        \
	      qrencode.o                    \
			  qrinput.o        \
			  split.o          \
			  bitstream.o     \
			  qrspec.o         \
			  mqrspec.o        \
			  rscode.o        \
			  mask.o          \
			  mmask.o       \
			  CreatQr.o     \
			  display.o
			

all: $(OBJS)
	$(CC) $(LDFLAGS) -o qrencode $^

clean:
	rm -f qrencode
	rm -f $(OBJS)

%.o:%.c
	$(CC) $(CFLAGS) -o $@ $<

