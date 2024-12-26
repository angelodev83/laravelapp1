<!DOCTYPE html>
<html lang="en">

<head>

</head>

<body>
    <div class="announcement-content">
        {!! $announcement->content !!}
    </div>
    <br><br>
    All the best,
    <br>
    <h4><strong>{{ $notifiable->employee->firstname }} {{ $notifiable->employee->lastname }}</strong></h4>
    <img src="https://home.mgmt88.com/images/mgmt88-black.png" class="logo" alt="Logo">
</body>
<style>
    .announcement-content img {
        max-width: 100%;
        height: auto;
    }

    .logo {
        max-width: 100%;
        max-height: 200px;
    }

    @media (max-width: 480px) {
        .logo {
            max-height: 150px;
        }
    }
</style>

</html>