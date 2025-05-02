BƯỚC 1: CÀI ĐẶT MICROSTACK
1. Cập nhật hệ thống
sudo apt update && sudo apt upgrade -y
2. Cài MicroStack bằng Snap
sudo snap install microstack --classic
3. Khởi tạo cấu hình MicroStack
sudo microstack init --auto --control
4. Kiểm tra trạng thái
microstack status
Nếu thấy dòng OpenStack services are running, bạn đã thành công!
BƯỚC 2: TRUY CẬP GIAO DIỆN WEB OPENSTACK
1. Lấy địa chỉ IP:
ip a
(tìm IP máy ảo trong dải 192.168.x.x hoặc 10.x.x.x)

2. Truy cập dashboard:
http://<IP máy ảo>:80
3. Đăng nhập
Username: admin
Password: keystone (mặc định MicroStack)

🔹 PHẦN 2: TẠO MÁY ẢO TRONG OPENSTACK
1️⃣ Tạo Mạng OpenStack
openstack network create private-net
openstack subnet create --network private-net --subnet-range 192.168.100.0/24 private-subnet

2️⃣ Tạo Image Ubuntu 22.04 cho VM
Tải image Ubuntu:
wget https://cloud-images.ubuntu.com/jammy/current/jammy-server-cloudimg-amd64.img
openstack image create --file jammy-server-cloudimg-amd64.img --disk-format qcow2 --container-format bare --public ubuntu-22.04

3️⃣ Tạo Security Group Cho Phép SSH
openstack security group rule create --proto tcp --dst-port 22 default

4️⃣ Tạo Máy Ảo (VM)
openstack server create --flavor m1.small --image ubuntu-22.04 --network private-net --security-group default my-vm

Kiểm tra:
openstack server list

🔹 PHẦN 3: KẾT NỐI VỚI AWS/GCP ĐỂ TẠO HYBRID CLOUD
1️⃣ Cài đặt OpenVPN để Kết Nối OpenStack với AWS
Trên OpenStack:
sudo apt install -y openvpn easy-rsa
Tạo VPN server:
openvpn --genkey --secret /etc/openvpn/static.key
nano /etc/openvpn/server.conf

Thêm nội dung:

dev tun
ifconfig 10.8.0.1 10.8.0.2
secret /etc/openvpn/static.key

Khởi động OpenVPN:
sudo systemctl start openvpn@server
2️⃣ Cấu Hình VPN trên AWS
Trên AWS:

Tạo VPN Gateway

Cấu hình Site-to-Site VPN

Nhập địa chỉ OpenStack VPN

Chọn BGP hoặc Static Routing

Bài bmtt
 Chặn một địa chỉ IP cụ thể (VD: 192.168.1.100)
netsh advfirewall firewall add rule name="ChanIP_tensv_IP" dir=in action=block remoteip=192.168.1.100
Chặn một cổng (VD: 445 - SMB)
netsh advfirewall firewall add rule name="ChanIP_tensv_Port" dir=in action=block protocol=TCP localport=445
Chặn một giao thức cụ thể (VD: ICMP - Ping)
netsh advfirewall firewall add rule name="ChanIP_tensv_ICMP" dir=in action=block protocol=ICMPv4
Chặn truy cập đến một địa chỉ IP cụ thể (VD: 203.0.113.10)
netsh advfirewall firewall add rule name="ChanIP_tensv_OutIP" dir=out action=block remoteip=203.0.113.10
Chặn một cổng (VD: 21 - FTP Upload)
netsh advfirewall firewall add rule name="ChanIP_tensv_OutPort" dir=out action=block protocol=TCP remoteport=21
Chặn một giao thức cụ thể (VD: UDP - DNS Requests)
netsh advfirewall firewall add rule name="ChanIP_tensv_OutUDP" dir=out action=block protocol=UDP remoteport=53
