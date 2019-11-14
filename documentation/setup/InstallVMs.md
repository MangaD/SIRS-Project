# Install Virtual Machines

**Note:** We use [Virtual Box](https://www.virtualbox.org/wiki/Downloads) for our VMs and [Lubuntu](https://lubuntu.net/downloads/) OS.

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
```

