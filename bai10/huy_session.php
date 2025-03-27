Cập nhật hệ thống và cài đặt công cụ hỗ trợ:
sudo apt update && sudo apt upgrade -y
sudo apt install -y net-tools curl vim git

Cài đặt OpenStack MicroStack:
sudo snap install microstack --classic

Khởi tạo OpenStack
sudo microstack init --auto --control

Kiểm tra trạng thái:
microstack.openstack service list

Truy cập Dashboard
Tìm địa chỉ IP của OpenStack:
ip a | grep "inet "

Mở trình duyệt vào địa chỉ:
➡ http://10.20.20.1 (hoặc IP khác tùy cấu hình)

Đăng nhập:
Username: admin
Password: Chạy lệnh để lấy mật khẩu:
sudo snap get microstack config.credentials.admin.password

Tạo Máy Ảo (Instance) trong OpenStack
microstack.openstack image list

Tạo mạng:
microstack.openstack network create test-net
microstack.openstack subnet create --network test-net --subnet-range 192.168.100.0/24 test-subnet

Tạo máy ảo:
microstack.openstack server create --flavor m1.tiny --image cirros --network test-net --security-group default my-vm

Kiểm tra:
microstack.openstack server list

Kết nối SSH vào Máy Ảo
ssh cirros@<INSTANCE_IP>
Lấy IP bằng:
microstack.openstack server list

KẾT NỐI VỚI AWS/GCP (HYBRID CLOUD)
Cài đặt OpenStack CLI
sudo apt install -y python3-openstackclient
Tạo kết nối VPN giữa OpenStack và AWS/GCP
Bước 1: Cấu hình OpenStack VPN
microstack.openstack vpn service create --router router1 --subnet test-subnet openstack-vpn
microstack.openstack vpn ipsec-site-connection create \
    --vpnservice openstack-vpn --ikepolicy ike-policy \
    --ipsecpolicy ipsec-policy --peer-address <AWS_VPN_IP> \
    --peer-id <AWS_VPN_IP> --peer-cidr 10.0.0.0/16 aws-vpn-connection

Bước 2: Cấu hình VPN trên AWS
