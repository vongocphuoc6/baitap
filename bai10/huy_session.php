sudo apt update
sudo apt install -y ros-humble-turtlebot3* 

echo "export TURTLEBOT3_MODEL=burger" >> ~/.bashrc
source ~/.bashrc

ros2 launch turtlebot3_gazebo turtlebot3_world.launch.py

ros2 launch turtlebot3_gazebo turtlebot3_house.launch.py

ros2 run turtlebot3_teleop teleop_keyboard
echo $TURTLEBOT3_MODEL
echo $ROS_DOMAIN_ID
echo $ROS_DOMAIN_ID
export ROS_DOMAIN_ID=0
export TURTLEBOT3_MODEL=burger
ros2 run turtlebot3_teleop teleop_keyboard

// cái mới
cd ~/ros2_ws/src
ros2 pkg create --build-type ament_python turtlebot3_keyboard_control
cd ~/ros2_ws/src/turtlebot3_keyboard_control/turtlebot3_keyboard_control
touch keyboard_control.py
chmod +x keyboard_control.py  # Cho phép file được thực thi
Mở file keyboard_control.py và dán nội dung sau:
cd ~/ros2_ws/src/turtlebot3_keyboard_control/turtlebot3_keyboard_control
nano keyboard_control.py
<!-- Sau khi mở file, bạn có thể chỉnh sửa nội dung.
Nhấn Ctrl + O, sau đó nhấn Enter để lưu.
Nhấn Ctrl + X để thoát. -->

import rclpy
from rclpy.node import Node
from geometry_msgs.msg import Twist
import sys
import termios
import tty

class KeyboardControl(Node):
    def __init__(self):
        super().__init__('keyboard_control')
        self.publisher_ = self.create_publisher(Twist, '/cmd_vel', 10)
        self.get_logger().info("Sử dụng các phím W/A/S/D để điều khiển. Nhấn Q để thoát.")

    def get_key(self):
        """
        Lấy đầu vào từ bàn phím theo thời gian thực.
        """
        tty.setraw(sys.stdin.fileno())  # Chuyển terminal về chế độ "raw"
        key = sys.stdin.read(1)         # Đọc một ký tự từ bàn phím
        termios.tcsetattr(sys.stdin, termios.TCSADRAIN, termios.tcgetattr(sys.stdin))  # Reset lại terminal
        return key

    def run(self):
        """
        Lắng nghe phím bấm và xuất lệnh điều khiển robot.
        """
        twist = Twist()  # Tin nhắn Twist dùng để gửi lệnh vận tốc
        while rclpy.ok():
            key = self.get_key()  # Lấy phím bấm từ người dùng
            if key.lower() == 'q':  # Nhấn Q để thoát
                self.get_logger().info("Thoát chương trình điều khiển.")
                break
            elif key.lower() == 'w':  # Tiến
                twist.linear.x = 0.2
                twist.angular.z = 0.0
            elif key.lower() == 's':  # Lùi
                twist.linear.x = -0.2
                twist.angular.z = 0.0
            elif key.lower() == 'a':  # Quẹo trái
                twist.linear.x = 0.0
                twist.angular.z = 0.5
            elif key.lower() == 'd':  # Quẹo phải
                twist.linear.x = 0.0
                twist.angular.z = -0.5
            else:  # Dừng robot nếu không nhấn phím điều khiển
                twist.linear.x = 0.0
                twist.angular.z = 0.0

            self.publisher_.publish(twist)  # Gửi lệnh đến robot

def main(args=None):
    rclpy.init(args=args)
    node = KeyboardControl()
    node.run()
    node.destroy_node()
    rclpy.shutdown()

if __name__ == '__main__':
    main()

// tt
cd ~/ros2_ws/src/turtlebot3_keyboard_control
nano setup.py

from setuptools import setup

package_name = 'turtlebot3_keyboard_control'

setup(
    name=package_name,
    version='0.0.1',
    packages=[package_name],
    data_files=[
        ('share/ament_index/resource_index/packages',
            ['resource/' + package_name]),
        ('share/' + package_name, ['package.xml']),
    ],
    install_requires=['setuptools'],
    zip_safe=True,
    maintainer='your_name',
    maintainer_email='your_email@example.com',
    description='Node điều khiển TurtleBot 3 bằng bàn phím.',
    license='Apache License 2.0',
    tests_require=['pytest'],
    entry_points={
        'console_scripts': [
            'keyboard_control = turtlebot3_keyboard_control.keyboard_control:main',
        ],
    },
)

//tt
nano package.xml
<depend>rclpy</depend>
<depend>geometry_msgs</depend>

//xong
cd ~/ros2_ws
colcon build
source install/setup.bash
ros2 launch turtlebot3_gazebo turtlebot3_world.launch.py
ros2 run turtlebot3_keyboard_control keyboard_control
ros2 topic echo /cmd_vel
