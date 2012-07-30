ncurses php framework

OOP interface for ncurses extension

h2. Install ncurses extension on Ubuntu from pecl

    sudo apt-get install php5-dev
    sudo apt-get install ncurses-dev
    sudo apt-get install libncursesw5-dev
    sudo pecl install ncurses
    sudo echo "extension=ncurses.so" > /etc/php5/cli/conf.d/ncurses.ini


Features:
    - Window
        - Title

    - Component
        - Button
        - Listbox
        - Checkbox
        - Radio

    - HotKeys
    - Signals
