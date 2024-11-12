sudo apt update
sudo apt install -y ros-humble-turtlebot3* 

echo "export TURTLEBOT3_MODEL=burger" >> ~/.bashrc
source ~/.bashrc

ros2 launch turtlebot3_gazebo turtlebot3_world.launch.py

ros2 launch turtlebot3_gazebo turtlebot3_house.launch.py

ros2 run turtlebot3_teleop teleop_keyboard

