'''
文件读取：
参考：https://www.cnblogs.com/youyou0/p/8921719.html   python txt常用读写操作

'''

#import numpy as np

def test():
    data = []
    for line in open("./urls.txt", "r"):
        line = line[:-1]
        data.append(line)
    for url in data:
        print(url)    # 有回车换行
def test2():
    #data = np.loadtxt("data.txt")
    data = []
    f = open("urls.txt", "r")  # 设置文件对象
    line = f.readline()
    line = line[:-1]
    while line:  # 直到读取完文件
        line = f.readline()  # 读取一行文件，包括换行符
        line = line[:-1]  # 去掉换行符，也可以不去
        data.append(line)
    f.close()  # 关闭文件
    for url in data:
        print(url)

# read file
def test3():
    with open('urls.txt',"r") as f:
        str = f.read()
if __name__ == "__main__":
    test2()