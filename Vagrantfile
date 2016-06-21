Vagrant.configure("2") do |config|
    config.vm.box_check_update = false
    config.vm.provider "docker" do |docker|
        docker.build_dir = "."
        docker.name = "vagrant"
        docker.ports = ["80:80"]
    end
end
