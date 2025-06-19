//neko.js - A cute cat following your cursor

/*
Thanks to jklee (https://juliaklee.wtf/) for the orginal code and design inspiration
*/


(function(window) {
  'use strict';

  const NekoCursor = {
    defaultConfig: {
      color: '#ffb7c5',
      size: 30,
      speed: 0.1,
      tailWag: true,
      autoStart: true
    },

    _neko: null,
    _mouseX: 0,
    _mouseY: 0,
    _nekoX: 0,
    _nekoY: 0,
    _isMoving: false,
    _hasMouseMoved: false,
    _moveTimeout: null,
    _animationId: null,
    _config: {},

    /**
     * @param {Object} options - Configuration options
     * @param {string} options.color - Cat color (default: '#ffb7c5')
     * @param {number} options.size - Cat size in pixels (default: 30)
     * @param {number} options.speed - Follow speed 0.1-0.3 (default: 0.1)
     * @param {boolean} options.tailWag - Enable tail wagging (default: true)
     * @param {boolean} options.autoStart - Auto-start on load (default: true)
     */
    init: function(options = {}) {
      this.destroy();

      this._config = Object.assign({}, this.defaultConfig, options);

      // Start cat off-screen (bottom-right corner, hidden)
      this._mouseX = 0;
      this._mouseY = 0;
      this._nekoX = -this._config.size * 2; // Start well off-screen
      this._nekoY = -this._config.size * 2;
      this._hasMouseMoved = false;

      this._createNeko();

      this._startAnimation();

      return this;
    },

    _createNeko: function() {
      const neko = document.createElement('div');
      neko.id = 'neko-cursor';
      neko.style.cssText = `
        position: fixed;
        width: ${this._config.size}px;
        height: ${this._config.size}px;
        pointer-events: none;
        z-index: 99999;
        transition: transform 0.1s ease-out;
        top: 0;
        left: 0;
        opacity: 0;
      `;

      const bodySize = this._config.size * 0.67;
      const earSize = this._config.size * 0.2;
      const tailLength = this._config.size * 0.4;
      const eyeSize = this._config.size * 0.067;

      neko.innerHTML = `
        <div style="width: ${bodySize}px; height: ${bodySize}px; background: ${this._config.color}; border-radius: 50%; position: absolute; top: ${this._config.size * 0.17}px; left: ${this._config.size * 0.17}px;"></div>
        <div style="width: 0; height: 0; border-left: ${earSize}px solid transparent; border-right: ${earSize}px solid transparent; border-bottom: ${earSize * 1.67}px solid ${this._config.color}; position: absolute; top: 0; left: ${this._config.size * 0.13}px; transform: rotate(-30deg);"></div>
        <div style="width: 0; height: 0; border-left: ${earSize}px solid transparent; border-right: ${earSize}px solid transparent; border-bottom: ${earSize * 1.67}px solid ${this._config.color}; position: absolute; top: 0; right: ${this._config.size * 0.13}px; transform: rotate(30deg);"></div>
        <div id="neko-tail" style="width: ${tailLength}px; height: ${this._config.size * 0.1}px; background: ${this._config.color}; position: absolute; bottom: ${this._config.size * 0.067}px; right: -${this._config.size * 0.27}px; transform-origin: left center; border-radius: ${this._config.size * 0.1}px; transition: transform 0.2s ease;"></div>
        <div style="position: absolute; top: ${this._config.size * 0.27}px; left: ${this._config.size * 0.27}px; width: ${this._config.size * 0.47}px; height: ${this._config.size * 0.27}px;">
          <div style="width: ${eyeSize}px; height: ${eyeSize}px; background: #000; border-radius: 50%; position: absolute; left: ${eyeSize}px; top: ${eyeSize}px;"></div>
          <div style="width: ${eyeSize}px; height: ${eyeSize}px; background: #000; border-radius: 50%; position: absolute; right: ${eyeSize}px; top: ${eyeSize}px;"></div>
          <div style="width: ${this._config.size * 0.13}px; height: ${eyeSize}px; border-bottom: 1px solid #000; border-radius: 50%; position: absolute; left: ${this._config.size * 0.17}px; top: ${this._config.size * 0.13}px;"></div>
        </div>
      `;

      document.body.appendChild(neko);
      this._neko = neko;
    },

    _constrainToViewport: function(x, y) {
      const margin = this._config.size / 2;
      return {
        x: Math.max(margin, Math.min(window.innerWidth - margin, x)),
        y: Math.max(margin, Math.min(window.innerHeight - margin, y))
      };
    },

    _startAnimation: function() {
      const self = this;

      function updateMouse(e) {
        self._mouseX = e.clientX;
        self._mouseY = e.clientY;
        self._isMoving = true;

        // Show cat when mouse moves for the first time
        if (!self._hasMouseMoved) {
          self._hasMouseMoved = true;
          if (self._neko) {
            self._neko.style.opacity = '1';
            self._neko.style.transition = 'transform 0.1s ease-out, opacity 0.3s ease-in';
          }
        }

        clearTimeout(self._moveTimeout);
        self._moveTimeout = setTimeout(() => {
          self._isMoving = false;
        }, 100);
      }

      function handleResize() {
        if (self._hasMouseMoved) {
          const constrained = self._constrainToViewport(self._nekoX, self._nekoY);
          self._nekoX = constrained.x;
          self._nekoY = constrained.y;
          
          const mouseConstrained = self._constrainToViewport(self._mouseX, self._mouseY);
          self._mouseX = mouseConstrained.x;
          self._mouseY = mouseConstrained.y;
        }
      }

      function animate() {
        if (!self._neko) return;

        // Only start following after mouse has moved
        if (self._hasMouseMoved) {
          const dx = self._mouseX - self._nekoX;
          const dy = self._mouseY - self._nekoY;

          self._nekoX += dx * self._config.speed;
          self._nekoY += dy * self._config.speed;

          const constrained = self._constrainToViewport(self._nekoX, self._nekoY);
          self._nekoX = constrained.x;
          self._nekoY = constrained.y;

          const angle = Math.atan2(dy, dx) * (180 / Math.PI);
          const distance = Math.sqrt(dx * dx + dy * dy);

          const translateX = self._nekoX - self._config.size / 2;
          const translateY = self._nekoY - self._config.size / 2;
          
          self._neko.style.transform = `translate(${translateX}px, ${translateY}px) ${
            self._isMoving && distance > 1 ? `rotate(${angle}deg) scale(1.1)` : 'scale(1)'
          }`;

          if (self._config.tailWag) {
            const tail = document.getElementById('neko-tail');
            if (tail) {
              tail.style.transform = self._isMoving 
                ? `rotate(${Math.sin(Date.now() * 0.01) * 20}deg)` 
                : 'rotate(0deg)';
            }
          }
        } else {
          // Keep cat off-screen until mouse moves
          const translateX = self._nekoX - self._config.size / 2;
          const translateY = self._nekoY - self._config.size / 2;
          self._neko.style.transform = `translate(${translateX}px, ${translateY}px)`;
        }

        self._animationId = requestAnimationFrame(animate);
      }

      document.addEventListener('mousemove', updateMouse);
      window.addEventListener('resize', handleResize);

      this._updateMouse = updateMouse;
      this._handleResize = handleResize;

      animate();
    },

    /**
     * @param {Object} newConfig - New configuration options
     */
    updateConfig: function(newConfig) {
      Object.assign(this._config, newConfig);
      
      if (this._neko) {
        this.destroy();
        this.init(this._config);
      }
      
      return this;
    },

    pause: function() {
      if (this._animationId) {
        cancelAnimationFrame(this._animationId);
        this._animationId = null;
      }
      return this;
    },

    resume: function() {
      if (!this._animationId && this._neko) {
        this._startAnimation();
      }
      return this;
    },

    destroy: function() {
      if (this._updateMouse) {
        document.removeEventListener('mousemove', this._updateMouse);
      }
      if (this._handleResize) {
        window.removeEventListener('resize', this._handleResize);
      }

      if (this._animationId) {
        cancelAnimationFrame(this._animationId);
        this._animationId = null;
      }

      const existing = document.getElementById('neko-cursor');
      if (existing) {
        existing.remove();
      }

      if (this._moveTimeout) {
        clearTimeout(this._moveTimeout);
        this._moveTimeout = null;
      }

      this._neko = null;
      this._updateMouse = null;
      this._handleResize = null;

      return this;
    },

    isActive: function() {
      return !!this._neko;
    }
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
      if (NekoCursor.defaultConfig.autoStart) {
        NekoCursor.init();
      }
    });
  } else {
    if (NekoCursor.defaultConfig.autoStart) {
      NekoCursor.init();
    }
  }

  window.NekoCursor = NekoCursor;

})(window);

// Usage examples:
// NekoCursor.init(); // Default pink cat
// NekoCursor.init({color: '#87CEEB', size: 40, speed: 0.15}); // Custom blue cat
// NekoCursor.updateConfig({color: '#FF6B6B'}); // Change to red
// NekoCursor.pause(); // Pause animation
// NekoCursor.resume(); // Resume animation
// NekoCursor.destroy(); // Remove completely

NekoCursor.init({color: '#58a6ff'});