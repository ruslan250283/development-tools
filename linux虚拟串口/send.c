#include <stdio.h>
      #include <string.h>
      #include <malloc.h>
      #include <sys/types.h>
      #include <sys/stat.h>
      #include <fcntl.h>
      #include <unistd.h>
      #include <termios.h>

      #define MAX_BUFFER_SIZE 512

      int fd, flag_close;

      int open_serial()
      {
            //这里的/dev/pts/1是使用mkptych.py虚拟的两个串口名字之一
            fd = open("/dev/pts/13", O_RDWR | O_NOCTTY | O_NONBLOCK);
            if(fd == -1)
            {
                  perror("open serial port error!\n");
                  return -1;
            }

            printf("Open serial port success!");
            return 0;
      }

      int main(int argc, char* argv[])
      {
            char sbuf[] = {"Hello, this is a serial port test!\n"};
            int retv;
            struct termios option;

            retv = open_serial();
            if(retv < 0)
            {
                  perror("open serial port error!\n");
                  return -1;
            }

            printf("Ready for sending data...\n");

            tcgetattr(fd, &option);
            cfmakeraw(&option);

            cfsetispeed(&option, B9600);
            cfsetospeed(&option, B9600);

            tcsetattr(fd, TCSANOW, &option);

            int length = sizeof(sbuf);
            retv = write(fd, sbuf, length);
            if(retv == -1)
            {
                  perror("Write data error!\n");
                  return -1;
            }

            printf("The number of char sent is %d\n", retv);
            return 0;
      }
