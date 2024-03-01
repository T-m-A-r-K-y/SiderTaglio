FROM ubuntu:20.04

# Install git and related
RUN apt-get update && \
    DEBIAN_FRONTEND=noninteractive apt-get install -yq \
        curl \
        git \
        zip \
        unzip \
        pkg-config \
        python3-dev \
        python3-pip \
        cmake \
        libboost-dev \
        libpolyclipping-dev \
        libnlopt-cxx-dev

# Install nest2D
# Add a non-root user
ARG USER="nest2d"
ENV USER=${USER}
ENV HOME /home/${USER}
RUN adduser --shell /bin/sh --disabled-password --gecos "" ${USER} 

USER ${USER}
WORKDIR ${HOME}

RUN git clone https://github.com/markfink/nest2D
WORKDIR ${HOME}/nest2D
RUN pip install nest2D

ENTRYPOINT bash
