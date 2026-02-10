/**
 * Tubes Cursor Effect for DekanPro Theme
 * Smooth flowing tubes that follow cursor movement
 * Lines spread apart while moving and form flower pattern when idle
 * Click on logo-inner to toggle: lines wrap logo / lines follow cursor
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
            this.prevMouse = { x: 0, y: 0 };
            this.tubes = [];
            this.tubeCount = 5;
            this.idleTime = 0;           // Время без движения
            this.isIdle = false;         // Флаг покоя
            this.globalTime = 0;         // Глобальное время для анимации
            this.linesFollowCursor = true;  // true = полоски за курсором, false = обвивают логотип
            this.logoInner = null;
            // Яркие неоновые цвета как в оригинале
            this.colors = [
                'rgba(249, 103, 251, 1)',    // Яркий розовый/маджента
                'rgba(83, 188, 40, 1)',      // Яркий зелёный
                'rgba(105, 88, 213, 1)',     // Фиолетовый
                'rgba(254, 138, 46, 1)',     // Оранжевый
                'rgba(255, 0, 138, 1)'       // Розовый
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
            this.setupLogoInnerToggle();
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
                z-index: 0;
                opacity: 1;
            `;
            document.body.appendChild(this.canvas);
            this.ctx = this.canvas.getContext('2d');
        }

        setupLogoInnerToggle() {
            this.logoInner = document.querySelector('.logo-inner');
            if (this.logoInner) {
                this.logoInner.style.cursor = 'pointer';
                
                this.logoInner.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.linesFollowCursor = !this.linesFollowCursor;
                });
            }
        }
        
        getLogoCenter() {
            if (!this.logoInner) return null;
            const rect = this.logoInner.getBoundingClientRect();
            return {
                x: rect.left + rect.width / 2,
                y: rect.top + rect.height / 2
            };
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
                this.prevMouse.x = this.width / 2;
                this.prevMouse.y = this.height / 2;
            }
        }

        initTubes() {
            this.tubes = [];
            const angleStep = (Math.PI * 2) / this.tubeCount;
            
            for (let i = 0; i < this.tubeCount; i++) {
                // Каждая трубка имеет свой угол смещения
                const angle = angleStep * i;
                this.tubes.push(new Tube(
                    this.width / 2,
                    this.height / 2,
                    this.colors[i % this.colors.length],
                    0.06 + i * 0.01,  // Скорость следования
                    6 + i * 1.5,      // Толщина линии (увеличена)
                    50 + i * 5,       // Длина следа
                    angle,            // Угол смещения
                    30 + i * 10,      // Максимальное расстояние расхождения
                    i                 // Индекс для уникальной анимации
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
                this.idleTime = 0;  // Сбрасываем счётчик покоя
                this.isIdle = false;
            });

            document.addEventListener('touchmove', (e) => {
                if (e.touches.length > 0) {
                    this.targetMouse.x = e.touches[0].clientX;
                    this.targetMouse.y = e.touches[0].clientY;
                    this.idleTime = 0;
                    this.isIdle = false;
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

            this.globalTime += 0.016; // ~60fps

            let targetX, targetY, speed, moveAngle, isIdle;
            
            if (this.linesFollowCursor) {
                // Режим следования за курсором
                this.prevMouse.x = this.mouse.x;
                this.prevMouse.y = this.mouse.y;
                this.mouse.x += (this.targetMouse.x - this.mouse.x) * 0.15;
                this.mouse.y += (this.targetMouse.y - this.mouse.y) * 0.15;
                const dx = this.mouse.x - this.prevMouse.x;
                const dy = this.mouse.y - this.prevMouse.y;
                speed = Math.sqrt(dx * dx + dy * dy);
                moveAngle = Math.atan2(dy, dx);
                if (speed < 0.5) {
                    this.idleTime += 0.016;
                    isIdle = this.idleTime > 0.5;
                } else {
                    this.idleTime = 0;
                    isIdle = false;
                }
                targetX = this.mouse.x;
                targetY = this.mouse.y;
            } else {
                // Режим обвивания логотипа — полоски вокруг лого
                const logoCenter = this.getLogoCenter();
                if (logoCenter) {
                    targetX = logoCenter.x;
                    targetY = logoCenter.y;
                    this.mouse.x = targetX;
                    this.mouse.y = targetY;
                } else {
                    targetX = this.width / 2;
                    targetY = this.height / 2;
                }
                speed = 0;
                moveAngle = 0;
                isIdle = true; // Всегда цветочный паттерн вокруг лого
            }

            // Очистка и отрисовка
            this.ctx.clearRect(0, 0, this.width, this.height);
            const wrapLogo = !this.linesFollowCursor;
            for (const tube of this.tubes) {
                tube.update(targetX, targetY, speed, moveAngle, isIdle, this.globalTime, wrapLogo);
                tube.draw(this.ctx);
            }

            this.animationId = requestAnimationFrame(() => this.animate());
        }
    }

    class Tube {
        constructor(x, y, color, speed, thickness, historyLength, angle, spreadDistance, index) {
            this.x = x;
            this.y = y;
            this.color = color;
            this.speed = speed;
            this.thickness = thickness;
            this.historyLength = historyLength;
            this.angle = angle;              // Угол смещения этой трубки
            this.spreadDistance = spreadDistance; // Максимальное расстояние расхождения
            this.currentSpread = 0;          // Текущее расхождение
            this.twistAngle = 0;             // Угол завихрения
            this.twistSpeed = 0.08 + Math.random() * 0.04; // Скорость вращения
            this.index = index;              // Индекс для уникальной анимации
            this.idlePhase = Math.random() * Math.PI * 2; // Случайная начальная фаза
            this.history = [];
            
            // Инициализируем историю
            for (let i = 0; i < this.historyLength; i++) {
                this.history.push({ x: x, y: y });
            }
        }

        update(targetX, targetY, moveSpeed, moveAngle, isIdle, globalTime, wrapLogo = false) {
            // Расхождение зависит от скорости движения
            const targetSpread = Math.min(moveSpeed * 3, this.spreadDistance);
            this.currentSpread += (targetSpread - this.currentSpread) * 0.1;
            
            // Завихрение - угол постоянно вращается
            this.twistAngle += this.twistSpeed;
            
            let offsetX = 0;
            let offsetY = 0;
            
            if (isIdle) {
                // Режим покоя / обвивание лого — цветок из переплетающихся лент
                const baseRadius = wrapLogo ? 70 : 25;  // Больше радиус при обвивании логотипа
                const flowerRadius = baseRadius + Math.sin(globalTime * 0.5 + this.index) * (wrapLogo ? 20 : 10);
                const petalCount = 5;
                const petalPhase = globalTime * 1.2 + this.idlePhase;
                
                // Создаём лепесток - движение по фигуре Лиссажу для эффекта плетения
                const a = 3; // Частота по X
                const b = 2; // Частота по Y
                const delta = (Math.PI * 2 / this.tubes?.length || 5) * this.index;
                
                // Фигура Лиссажу создаёт красивое переплетение
                const lissajousX = Math.sin(a * petalPhase + delta) * flowerRadius;
                const lissajousY = Math.sin(b * petalPhase) * flowerRadius;
                
                // Добавляем вращение всего цветка
                const rotationAngle = globalTime * 0.3;
                offsetX = lissajousX * Math.cos(rotationAngle) - lissajousY * Math.sin(rotationAngle);
                offsetY = lissajousX * Math.sin(rotationAngle) + lissajousY * Math.cos(rotationAngle);
                
                // Добавляем небольшое "дыхание" - пульсацию
                const breathe = 1 + Math.sin(globalTime * 2) * 0.1;
                offsetX *= breathe;
                offsetY *= breathe;
                
            } else {
                // Режим движения - расхождение и завихрение
                const twistStrength = Math.min(moveSpeed * 0.8, 15);
                const perpAngle = moveAngle + Math.PI / 2 + this.angle;
                
                const twistOffsetX = Math.cos(this.twistAngle) * twistStrength;
                const twistOffsetY = Math.sin(this.twistAngle) * twistStrength;
                
                offsetX = Math.cos(perpAngle) * this.currentSpread + twistOffsetX;
                offsetY = Math.sin(perpAngle) * this.currentSpread + twistOffsetY;
            }
            
            // Плавное движение к смещённой цели
            this.x += (targetX + offsetX - this.x) * this.speed;
            this.y += (targetY + offsetY - this.y) * this.speed;

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
            
            // Яркий градиент с плавным затуханием
            const baseColor = this.color.replace(/[\d.]+\)$/, '');
            gradient.addColorStop(0, baseColor + '1)');
            gradient.addColorStop(0.3, baseColor + '0.8)');
            gradient.addColorStop(0.6, baseColor + '0.4)');
            gradient.addColorStop(1, baseColor + '0)');

            // Рассеянные края - несколько слоёв с разной толщиной и прозрачностью
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            
            // Внешний рассеянный слой (самый размытый)
            ctx.shadowColor = this.color;
            ctx.shadowBlur = 25;
            ctx.strokeStyle = gradient;
            ctx.lineWidth = this.thickness * 2;
            ctx.globalAlpha = 0.3;
            ctx.stroke();
            
            // Средний слой
            ctx.shadowBlur = 15;
            ctx.lineWidth = this.thickness * 1.5;
            ctx.globalAlpha = 0.5;
            ctx.stroke();
            
            // Основная линия
            ctx.shadowBlur = 8;
            ctx.lineWidth = this.thickness;
            ctx.globalAlpha = 1;
            ctx.stroke();
            
            // Яркая сердцевина
            ctx.shadowBlur = 0;
            ctx.lineWidth = this.thickness * 0.4;
            ctx.globalAlpha = 0.8;
            ctx.stroke();
            
            ctx.globalAlpha = 1;
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
