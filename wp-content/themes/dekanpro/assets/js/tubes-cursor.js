/**
 * Tubes Cursor Effect for DekanPro Theme
 * Smooth flowing tubes that follow cursor movement
 */

(function() {
    'use strict';

    class TubesCursorEffect {
        constructor() {
            this.canvas = null;
            this.ctx = null;
            this.width = 0;
            this.height = 0;
            this.mouse = { x: 0, y: 0 };
            this.targetMouse = { x: 0, y: 0 };
            this.tubes = [];
            this.tubeCount = 6;
            this.colors = [
                'rgba(99, 102, 241, 0.8)',   // Indigo - ярче
                'rgba(139, 92, 246, 0.8)',   // Violet
                'rgba(168, 85, 247, 0.8)',   // Purple
                'rgba(96, 165, 250, 0.7)',   // Blue
                'rgba(236, 72, 153, 0.7)',   // Pink
                'rgba(34, 211, 238, 0.7)'    // Cyan
            ];
            this.animationId = null;
            this.isRunning = false;
            
            this.init();
        }

        init() {
            this.createCanvas();
            this.resize();
            this.initTubes();
            this.bindEvents();
            this.start();
        }

        createCanvas() {
            this.canvas = document.createElement('canvas');
            this.canvas.id = 'dekanpro-tubes-canvas';
            this.canvas.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                pointer-events: none;
                z-index: 9999;
                opacity: 1;
            `;
            document.body.appendChild(this.canvas);
            this.ctx = this.canvas.getContext('2d');
        }

        resize() {
            this.width = window.innerWidth;
            this.height = window.innerHeight;
            this.canvas.width = this.width;
            this.canvas.height = this.height;
            
            // Центрируем мышь при старте
            if (this.mouse.x === 0 && this.mouse.y === 0) {
                this.mouse.x = this.width / 2;
                this.mouse.y = this.height / 2;
                this.targetMouse.x = this.width / 2;
                this.targetMouse.y = this.height / 2;
            }
        }

        initTubes() {
            this.tubes = [];
            for (let i = 0; i < this.tubeCount; i++) {
                this.tubes.push(new Tube(
                    this.width / 2,
                    this.height / 2,
                    this.colors[i % this.colors.length],
                    0.03 + i * 0.02, // Разная скорость следования (медленнее для более плавного эффекта)
                    8 + i * 4,       // Разная толщина
                    80 + i * 15      // Разная длина истории (длиннее для более выраженного следа)
                ));
            }
        }

        bindEvents() {
            window.addEventListener('resize', () => {
                this.resize();
            });

            document.addEventListener('mousemove', (e) => {
                this.targetMouse.x = e.clientX;
                this.targetMouse.y = e.clientY;
            });

            document.addEventListener('touchmove', (e) => {
                if (e.touches.length > 0) {
                    this.targetMouse.x = e.touches[0].clientX;
                    this.targetMouse.y = e.touches[0].clientY;
                }
            }, { passive: true });

            // Останавливаем анимацию когда вкладка неактивна
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    this.stop();
                } else {
                    this.start();
                }
            });
        }

        start() {
            if (this.isRunning) return;
            this.isRunning = true;
            this.animate();
        }

        stop() {
            this.isRunning = false;
            if (this.animationId) {
                cancelAnimationFrame(this.animationId);
                this.animationId = null;
            }
        }

        animate() {
            if (!this.isRunning) return;

            // Плавное следование за мышью
            this.mouse.x += (this.targetMouse.x - this.mouse.x) * 0.15;
            this.mouse.y += (this.targetMouse.y - this.mouse.y) * 0.15;

            // Очистка с прозрачным фоном
            this.ctx.clearRect(0, 0, this.width, this.height);

            // Обновляем и рисуем трубки
            for (const tube of this.tubes) {
                tube.update(this.mouse.x, this.mouse.y);
                tube.draw(this.ctx);
            }

            this.animationId = requestAnimationFrame(() => this.animate());
        }
    }

    class Tube {
        constructor(x, y, color, speed, thickness, historyLength) {
            this.x = x;
            this.y = y;
            this.color = color;
            this.speed = speed;
            this.thickness = thickness;
            this.historyLength = historyLength;
            this.history = [];
            
            // Инициализируем историю
            for (let i = 0; i < this.historyLength; i++) {
                this.history.push({ x: x, y: y });
            }
        }

        update(targetX, targetY) {
            // Плавное движение к цели
            this.x += (targetX - this.x) * this.speed;
            this.y += (targetY - this.y) * this.speed;

            // Добавляем новую позицию в начало
            this.history.unshift({ x: this.x, y: this.y });
            
            // Удаляем старые позиции
            if (this.history.length > this.historyLength) {
                this.history.pop();
            }
        }

        draw(ctx) {
            if (this.history.length < 2) return;

            ctx.beginPath();
            ctx.moveTo(this.history[0].x, this.history[0].y);

            // Рисуем плавную кривую через все точки
            for (let i = 1; i < this.history.length - 1; i++) {
                const xc = (this.history[i].x + this.history[i + 1].x) / 2;
                const yc = (this.history[i].y + this.history[i + 1].y) / 2;
                ctx.quadraticCurveTo(this.history[i].x, this.history[i].y, xc, yc);
            }

            // Градиент толщины
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            
            // Создаём градиент вдоль линии
            const gradient = ctx.createLinearGradient(
                this.history[0].x, 
                this.history[0].y,
                this.history[this.history.length - 1].x,
                this.history[this.history.length - 1].y
            );
            
            // Парсим цвет для градиента
            const baseColor = this.color.replace(/[\d.]+\)$/, '');
            gradient.addColorStop(0, baseColor + '1)');
            gradient.addColorStop(0.3, baseColor + '0.7)');
            gradient.addColorStop(0.7, baseColor + '0.3)');
            gradient.addColorStop(1, baseColor + '0)');

            ctx.strokeStyle = gradient;
            ctx.lineWidth = this.thickness;
            ctx.stroke();

            // Добавляем свечение
            ctx.shadowColor = this.color;
            ctx.shadowBlur = 25;
            ctx.stroke();
            ctx.shadowBlur = 0;
        }
    }

    // Функция инициализации
    function initEffect() {
        // Проверяем, не в админке ли мы
        if (document.body.classList.contains('wp-admin') || 
            document.body.classList.contains('login') ||
            window.location.pathname.includes('/wp-admin/') ||
            window.location.pathname.includes('/wp-login')) {
            return;
        }
        
        try {
            new TubesCursorEffect();
        } catch (error) {
            console.warn('DekanPro: Tubes cursor effect failed to initialize', error);
        }
    }

    // Запускаем после загрузки DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initEffect);
    } else {
        initEffect();
    }
})();
