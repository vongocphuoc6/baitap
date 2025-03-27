🔹 Bước 1: Cài đặt các gói cần thiết
sudo apt install -y git vim net-tools curl

🔹 Bước 2: Clone DevStack từ GitHub
git clone https://opendev.org/openstack/devstack.git
cd devstack

🔹 Bước 3: Tạo file cấu hình local.conf
nano local.conf
Dán nội dung sau:

[[local|localrc]]
ADMIN_PASSWORD=admin
DATABASE_PASSWORD=$ADMIN_PASSWORD
RABBIT_PASSWORD=$ADMIN_PASSWORD
SERVICE_PASSWORD=$ADMIN_PASSWORD
HOST_IP=192.168.1.100
💡 Lưu ý: Thay 192.168.1.100 bằng địa chỉ IP thực của máy bạn (ip a để kiểm tra).

🔹 Bước 4: Cài đặt OpenStack
./stack.sh
⏳ Quá trình này sẽ mất 15-30 phút. Sau khi hoàn thành, bạn có thể truy cập OpenStack qua trình duyệt:
➡ http://192.168.1.100/dashboard

Đăng nhập với:
Username: admin
Password: admin

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
