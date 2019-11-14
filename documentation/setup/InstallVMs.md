# Install Virtual Machines

**Note:** We use [VirtualBox](https://www.virtualbox.org/wiki/Downloads) for our VMs and [Lubuntu](https://lubuntu.net/downloads/) OS.

We will setup 4 VMs:

- Smartphone VM
- User VM
- Gateway VM
- Server VM

*Smartphone VM, User VM and Gateway VM are connected in a virtual network.
Gateway VM and Server VM are connected in another virtual network.
Gateway VM will operate as a gateway between the two subnets, and as a gateway to the Internet.*

## Create 1st VM

Create a new VM, install Lubuntu OS and Virtual Box Guest additions.

Install utilities:

```sh
sudo apt update && sude apt upgrade

# ifconfig and other tools
sudo apt install net-tools

# For persistent IP tables
sudo apt install iptables-persistent
```

## Create other VMs

- On the first VM click `Clone`
- Select the name you want to give to the new machine
- MAC Adress Policy: Generate new MAC addresses for all network adapters
- Select `Linked Clone`

Repeat this process for the other VMs and lets call the original machine Smartphone VM, and the new ones User VM, Gateway VM and Server VM.

## Network Interfaces

In order to connect the Smartphone VM, User VM and Gateway VM in the same network do the following in the VirtualBox interface with the VM turned off:

- Select the VM Settings/Network/Adapter1
- Attach to `Internal Network`. Call it sw-1 for example.
- Promisuous Mode: Allow VMs

Repeat for Gateway VM and Server VM but creating a second Network adapter in Gateway VM and calling the `Internal Network` sw-2.

Finally, create a third Network adapter in Gateway VM that is `nat`-ed with your physical address. 
This interface will be used to access the Internet.

## Configure virtual networks

We will now configure the IP network (supported by virtual switches sw-1 and sw-2) with static IP addresses. 
Smartphone VM, User VM and Gateway VM will talk using a subnet (sw-1). Gateway VM and Server VM will talk using another subnet (sw-2).

For sw-1 we will use the private IP addresses 192.168.0.0/24 (meaning that the subnet mask is 255.255.255.0 – we can have 254 addresses to use (from 192.168.0.1 to 192.168.0.254). Note that 192.168.0.255 is reserved for broadcast).

For sw-2 we will use the private IP addresses 192.168.1.0/24

The IP address of Smartphone VM will be 192.168.0.10, of User VM will be 192.168.0.20, and of Gateway VM will be 192.168.0.100 for the sw-1 subnet.

The IP address of Gateway VM will be 192.168.1.254 and the address of Server VM will be 192.168.1.1 for the sw-2 subnet. 

We are assuming that every VM except Server VM has an interface `enp0s3`(connected to sw-1) and Gateway VM has interfaces `enp0s3` (connected to sw-1), `enp0s8` (connected to sw-2), and `enp0s9` (connected to the internet). 
These `en...` values are the network interface names and are automatically assigned by the operating system following a [device naming convention](https://en.wikipedia.org/wiki/Consistent_Network_Device_Naming).

_How do you know which interface is connected to sw-1 and which one is connected to sw-2? 
Look at their MAC Addresses.  
Running `ip a` shows the MAC address of each interface and you can compare with those of VirtualBox._

### Netplan

**Smartphone VM (and User VM):**

```sh
sudo su
nano /etc/netplan/01-network-manager-all.yaml
```

Paste:
```
network:
  version: 2
  renderer: NetworkManager
  ethernets:
    enp0s3:
      dhcp4: no
      dhcp6: no
      addresses: [192.168.0.10/24]
      gateway4: 192.168.0.100
      nameservers:
        addresses: [1.1.1.1, 1.0.0.1]
```

_Note:  
1.1.1.1, 1.0.0.1 -> cloud flare dns  
8.8.8.8, 8.8.4.4 -> google dns_

```sh
sudo netplan try
sudo netplan apply
sudo systemctl restart network-manager
```

Repeat the same for User VM but with IP 192.168.0.20.

**Gateway VM:**

```sh
sudo su
nano /etc/netplan/01-network-manager-all.yaml
```

Paste:
```
network:
  version: 2
  renderer: NetworkManager
  ethernets:
    enp0s3:
      dhcp4: no
      dhcp6: no
      addresses: [192.168.0.100/24]
      nameservers:
        addresses: [1.1.1.1, 1.0.0.1]
    enp0s8:
      dhcp4: no
      dhcp6: no
      addresses: [192.168.1.254/24]
      nameservers:
        addresses: [1.1.1.1, 1.0.0.1]
    enp0s9:
      dhcp4: yes
      dhcp6: yes
      nameservers:
        addresses: [1.1.1.1, 1.0.0.1]
```

```sh
sudo netplan try
sudo netplan apply
sudo systemctl restart network-manager

# Uncomment 'net.ipv4.ip_forward' on '/etc/sysctl.conf'

sudo iptables -P FORWARD ACCEPT
sudo iptables -F FORWARD
sudo iptables -t nat -F
sudo iptables -t nat -A POSTROUTING  -o enp0s9 -j MASQUERADE

sudo apt install iptables-persistent

sudo su
iptables-save > /etc/iptables/rules.v4
exit
```

**Server VM:**

```sh
sudo su
nano /etc/netplan/01-network-manager-all.yaml
```

Paste:
```
network:
  version: 2
  renderer: NetworkManager
  ethernets:
    enp0s8:
      dhcp4: no
      dhcp6: no
      addresses: [192.168.1.1/24]
      gateway4: 192.168.1.254
      nameservers:
        addresses: [1.1.1.1, 1.0.0.1]
```

_Note: If you used Adapter 1 for the virtual network you might use enp0s3 interface._

```sh
sudo netplan try
sudo netplan apply
sudo systemctl restart network-manager
```