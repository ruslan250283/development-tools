CC = gcc-4.6
LD = gcc-4.6
CFLAGS = -Wall -c -Dgliethttp -I.
LDFLAGS = -lpthread -lpng

SRCS = $(wildcard *.c)
OBJS = $(patsubst %c,%o,$(SRCS))
TARGET = gliethttp

.PHONY: all clean

all: $(TARGET)

$(TARGET): $(OBJS)
	$(LD) $(LDFLAGS) -o $@ $^

%o: %c
	$(CC) $(CFLAGS) -o $@ $<
clean:
	@rm -f *.o $(TARGET)