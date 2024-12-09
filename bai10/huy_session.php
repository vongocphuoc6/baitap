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
//
sudo apt update
sudo apt install python3-colcon-common-extensions
colcon --help
source ~/ros2_ws/install/setup.bash
ros2 pkg list | grep turtlebot3_keyboard_control

cd ~/ros2_ws
rm -rf build install log
colcon build
source ~/ros2_ws/install/setup.bash
ros2 run turtlebot3_keyboard_control keyboard_control

//tt
import rclpy
from rclpy.node import Node
from geometry_msgs.msg import Twist
from sensor_msgs.msg import LaserScan
import threading
import sys
import termios
import tty

class TurtleBotControl(Node):
    def __init__(self):
        super().__init__('turtlebot_control')

        # Publisher cho vận tốc
        self.publisher_ = self.create_publisher(Twist, 'cmd_vel', 10)
        
        # Subscriber cho dữ liệu LaserScan
        self.subscriber_ = self.create_subscription(LaserScan, 'scan', self.scan_callback, 10)
        
        # Lệnh vận tốc
        self.cmd = Twist()

        # Biến để lưu chế độ hoạt động: True = tự hành, False = bàn phím
        self.autonomous_mode = True

        # Vận tốc mặc định
        self.forward_speed = 0.2
        self.turn_speed = 0.5

        # Bắt đầu luồng đọc bàn phím
        threading.Thread(target=self.keyboard_listener, daemon=True).start()

    def scan_callback(self, msg):
        """
        Xử lý dữ liệu LaserScan để tự hành tránh vật cản (chỉ khi ở chế độ tự hành).
        """
        if not self.autonomous_mode:
            return  # Không làm gì nếu đang ở chế độ điều khiển bằng bàn phím

        # Lấy khoảng cách nhỏ nhất ở phía trước
        min_distance = min(msg.ranges[:30] + msg.ranges[-30:])
        obstacle_threshold = 0.5  # Ngưỡng phát hiện vật cản (đơn vị: mét)

        # Nếu phát hiện vật cản, quay đầu; nếu không, tiến về phía trước
        if min_distance < obstacle_threshold:
            self.get_logger().info('Obstacle detected! Turning...')
            self.cmd.linear.x = 0.0
            self.cmd.angular.z = self.turn_speed  # Quay trái
        else:
            self.get_logger().info('Path clear. Moving forward...')
            self.cmd.linear.x = self.forward_speed
            self.cmd.angular.z = 0.0

        # Gửi lệnh vận tốc
        self.publisher_.publish(self.cmd)

    def keyboard_listener(self):
        """
        Lắng nghe lệnh từ bàn phím và chuyển đổi chế độ hoặc điều khiển robot.
        """
        settings = termios.tcgetattr(sys.stdin)
        try:
            tty.setcbreak(sys.stdin.fileno())
            print("Keyboard listener started. Press 'm' to toggle modes, or use WASD/arrows to control.")
            print("Press Ctrl+C to stop.")

            while True:
                key = sys.stdin.read(1)  # Đọc một phím

                if key == 'm':  # Chuyển chế độ
                    self.autonomous_mode = not self.autonomous_mode
                    mode = "Autonomous" if self.autonomous_mode else "Keyboard Control"
                    print(f"Switched to {mode} mode.")
                elif not self.autonomous_mode:  # Chế độ điều khiển bàn phím
                    self.cmd = Twist()  # Reset vận tốc trước khi gán
                    if key in ['w', '\x1b[A']:  # Tiến (W hoặc phím mũi tên lên)
                        self.cmd.linear.x = self.forward_speed
                    elif key in ['s', '\x1b[B']:  # Lùi (S hoặc phím mũi tên xuống)
                        self.cmd.linear.x = -self.forward_speed
                    elif key in ['a', '\x1b[D']:  # Quay trái (A hoặc phím mũi tên trái)
                        self.cmd.angular.z = self.turn_speed
                    elif key in ['d', '\x1b[C']:  # Quay phải (D hoặc phím mũi tên phải)
                        self.cmd.angular.z = -self.turn_speed
                    elif key == ' ':  # Dừng (phím Space)
                        self.cmd.linear.x = 0.0
                        self.cmd.angular.z = 0.0
                    else:
                        continue
                    
                    self.publisher_.publish(self.cmd)  # Gửi lệnh vận tốc
        finally:
            termios.tcsetattr(sys.stdin, termios.TCSADRAIN, settings)

def main(args=None):
    rclpy.init(args=args)
    turtlebot_control = TurtleBotControl()
    try:
        rclpy.spin(turtlebot_control)
    except KeyboardInterrupt:
        print("Shutting down...")
    finally:
        turtlebot_control.destroy_node()
        rclpy.shutdown()

if __name__ == '__main__':
    main()

//
'hybrid_control = turtlebot3_keyboard_control.hybrid_control:main',

            cd ~/ros2_ws
colcon build
source ~/ros2_ws/install/setup.bash
            ros2 run turtlebot3_keyboard_control hybrid_control
