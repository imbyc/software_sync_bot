import os
from lanzou.api import LanZouCloud

lanzou_username = os.getenv('LANZOU_USERNAME')
lanzou_password = os.getenv('LANZOU_PASSWORD')

lzy = LanZouCloud()
code = lzy.login(lanzou_username, lanzou_password)
if code == LanZouCloud.SUCCESS:
    print('登录成功')
elif code == LanZouCloud.FAILED
    print('登录失败')
elif code == LanZouCloud.NETWORK_ERROR
    print('网络异常')



# 设置rar路径
code = lzy.set_rar_tool('/usr/bin/rar')
if code == LanZouCloud.SUCCESS:
    print('RAR路径设置成功')
elif code == LanZouCloud.ZIP_ERROR
    print('RAR路径设置失败')