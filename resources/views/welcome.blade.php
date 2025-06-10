<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Chatly - Welcome</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-20px); }
    }

    @keyframes gradient {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .animated-bg {
      background: linear-gradient(-45deg, #3b82f6, #60a5fa, #1e3a8a, #2563eb);
      background-size: 400% 400%;
      animation: gradient 15s ease infinite;
    }

    .chat-bubble::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 20px;
      border-width: 10px;
      border-style: solid;
      border-color: white transparent transparent transparent;
    }
  </style>
</head>
<body class="animated-bg min-h-screen flex items-center justify-center overflow-hidden text-white">

  <!-- Floating Chat Bubbles -->
  <div class="absolute top-20 left-10 w-32 h-16 bg-white bg-opacity-20 rounded-xl shadow-lg backdrop-blur-md animate-[float_6s_ease-in-out_infinite]"></div>
  <div class="absolute bottom-20 right-10 w-24 h-12 bg-white bg-opacity-20 rounded-xl shadow-lg backdrop-blur-md animate-[float_8s_ease-in-out_infinite] delay-500"></div>

  <!-- Hero Card -->
  <div class="z-10 max-w-5xl w-full mx-6 md:mx-auto bg-white/20 backdrop-blur-2xl rounded-3xl shadow-2xl px-10 py-16 md:px-20 md:py-20 text-center md:text-left">

    <div class="flex flex-col md:flex-row items-center justify-between space-y-10 md:space-y-0 md:space-x-12">
      <!-- Left Content -->
      <div class="md:w-1/2 space-y-6">
        <h1 class="text-5xl md:text-6xl font-extrabold leading-tight tracking-tight text-white drop-shadow">
          Welcome to <span class="text-blue-200">Chatly</span>
        </h1>
        <p class="text-lg md:text-xl text-blue-100">
          A place where conversations come alive. Start chatting instantly and stay connected in real time.
        </p>
        <div class="flex flex-col md:flex-row gap-4 pt-6 justify-center md:justify-start">
          <a href="/login" class="px-8 py-3 bg-white text-blue-700 font-semibold rounded-xl shadow hover:bg-blue-100 transition">
            Login
          </a>
          <a href="/register" class="px-8 py-3 border border-white text-white font-semibold rounded-xl hover:bg-white hover:text-blue-700 transition">
            Register
          </a>
        </div>
      </div>

      <!-- Right Content -->
      <div class="md:w-1/2">
        <img src="https://cdn-icons-png.flaticon.com/512/1717/1717698.png" alt="Chat Illustration" class="w-72 mx-auto drop-shadow-lg" />
      </div>
    </div>
  </div>

</body>
</html>
