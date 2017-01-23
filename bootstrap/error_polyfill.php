<?php

if (!interface_exists('Throwable', false)) {
    interface Throwable
    {
    }
}

if (!class_exists('Error', false)) {
    class Error extends Exception implements Throwable
    {
    }
}
