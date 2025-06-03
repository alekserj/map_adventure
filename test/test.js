import {
  setMapInstance,
  setupRouteTypeButtons,
  optimizeRoute,
  addRoutePoint,
  drawCustomRoute,
  resetRoute,
  removeRoutePoint,
  setupResetButton,
  initInstructionsToggle,
  getDistance,
  formatTime
} from '../js/routeHandler.js';

document.body.innerHTML = `
  <div id="route-menu">
    <button class="route-btn" data-type="auto"></button>
    <button class="route-btn" data-type="pedestrian"></button>
    <button id="optimize-route-btn"></button>
    <button id="reset-route"></button>
    <div id="route-list"></div>
    <div id="route-info"></div>
    <button id="toggle-instructions-btn"></button>
    <div id="navigation-instructions"></div>
    <button id="addFavoriteRoute"></button>
    <div id="route-name-modal" style="display: none;">
      <button id="close-route-name-modal"></button>
      <button id="confirm-route-name"></button>
      <input id="route-name-input" />
    </div>
  </div>
`;

global.ymaps = {
  multiRouter: {
    MultiRoute: jest.fn().mockImplementation(() => ({
      properties: {
        set: jest.fn(),
        get: jest.fn()
      },
      model: {
        events: {
          add: jest.fn()
        }
      },
      getActiveRoute: jest.fn().mockReturnValue({
        properties: {
          get: jest.fn().mockImplementation((prop) => {
            if (prop === 'distance') return { value: 5000 };
            if (prop === 'duration') return { value: 1800 };
          })
        },
        getPaths: jest.fn().mockReturnValue({
          getLength: jest.fn().mockReturnValue(1),
          get: jest.fn().mockReturnValue({
            getSegments: jest.fn().mockReturnValue({
              getLength: jest.fn().mockReturnValue(1),
              get: jest.fn().mockReturnValue({
                properties: {
                  get: jest.fn().mockReturnValue('Поверните налево')
                }
              })
            })
          })
        })
      })
    }))
  },
  geocode: jest.fn().mockReturnValue({
    then: jest.fn(callback => callback({
      geoObjects: {
        get: jest.fn().mockReturnValue({
          getAddressLine: jest.fn().mockReturnValue('Курск, ул. Ватутина, 1')
        })
      }
    }))
  })
};

global.fetch = jest.fn();
global.alert = jest.fn();
global.navigator.geolocation = {
  getCurrentPosition: jest.fn()
};

class Sortable {
  constructor() {}
}
global.Sortable = Sortable;

describe('routeHandler', () => {
  beforeEach(() => {
    jest.clearAllMocks();
    document.getElementById('route-list').innerHTML = '';
    document.getElementById('route-info').innerHTML = '';
    document.getElementById('navigation-instructions').innerHTML = '';
  });

  describe('setMapInstance', () => {
    it(() => {
      const mockMap = {};
      setMapInstance(mockMap);
      expect(mapInstance).toBe(mockMap);
    });
  });

  describe('setupRouteTypeButtons', () => {
    it(() => {
      setupRouteTypeButtons();
      const buttons = document.querySelectorAll('.route-btn');
      
      buttons[0].click();
      expect(currentRouteType).toBe('auto');
      expect(buttons[0].classList.contains('active')).toBe(true);
      
      buttons[1].click();
      expect(currentRouteType).toBe('pedestrian');
      expect(buttons[1].classList.contains('active')).toBe(true);
    });

    it('обработка кнопки оптимизации маршрута', () => {
      setupRouteTypeButtons();
      const optimizeBtn = document.getElementById('optimize-route-btn');
      optimizeBtn.click();
      expect(optimizeRoute).toHaveBeenCalled();
    });
  });

  describe('optimizeRoute', () => {
    it('оптимизация точек маршрута по расстоянию', () => {
      routePoints = [
        { coords: [55.751244, 37.618423], name: 'Точка А' },
        { coords: [55.753930, 37.621407], name: 'Точка Б' },
        { coords: [55.752705, 37.621583], name: 'Точка В' }
      ];
      
      optimizeRoute();
      
      expect(routePoints[0].name).toBe('Точка А');
    });
  });

  describe('getDistance', () => {
    it('расчет расстояния между двумя точками', () => {
      const coord1 = [55.751244, 37.618423];
      const coord2 = [55.753930, 37.621407];
      
      const distance = getDistance(coord1, coord2);
      expect(distance).toBeCloseTo(0.25, 1);
    });
  });

  describe('addRoutePoint', () => {
    it('точка геолокации', () => {
      const mockPosition = {
        coords: {
          latitude: 55.751244,
          longitude: 37.618423
        }
      };
      navigator.geolocation.getCurrentPosition.mockImplementationOnce((success) => success(mockPosition));
      
      addRoutePoint([55.753930, 37.621407], 'Тестовая точка');
      
      expect(routePoints.length).toBe(2);
      expect(routePoints[0].isStartPoint).toBe(true);
      expect(routePoints[1].name).toBe('Тестовая точка');
    });

    it('обработка ошибки геолокации', () => {
      navigator.geolocation.getCurrentPosition.mockImplementationOnce((_, error) => error({ message: 'Ошибка' }));
      
      addRoutePoint([55.753930, 37.621407], 'Тестовая точка');
      
      expect(routePoints.length).toBe(2);
      expect(routePoints[0].coords).toEqual([51.7347, 36.1907]);
    });
  });

  describe('drawCustomRoute', () => {
    it('маршрут с 1 точкой', () => {
      routePoints = [{ coords: [55.751244, 37.618423], name: 'Точка А' }];
      drawCustomRoute();
      expect(ymaps.multiRouter.MultiRoute).not.toHaveBeenCalled();
    });

    it('маршрут с 2 точками', () => {
      routePoints = [
        { coords: [55.751244, 37.618423], name: 'Точка А' },
        { coords: [55.753930, 37.621407], name: 'Точка Б' }
      ];
      drawCustomRoute();
      expect(ymaps.multiRouter.MultiRoute).toHaveBeenCalled();
    });
  });

  describe('formatTime', () => {
    it('форматирование времени маршрута', () => {
      expect(formatTime(45)).toBe('45 мин');
      expect(formatTime(90)).toBe('1 ч 30 мин');
      expect(formatTime(125)).toBe('2 ч 5 мин');
    });
  });

  describe('resetRoute', () => {
    it('удаление точек и маршрута', () => {
      routePoints = [
        { coords: [55.751244, 37.618423], name: 'Точка А' },
        { coords: [55.753930, 37.621407], name: 'Точка Б' }
      ];
      currentRoute = {};
      
      resetRoute();
      
      expect(routePoints.length).toBe(0);
      expect(currentRoute).toBeNull();
    });
  });

  describe('removeRoutePoint', () => {
    it('удаление точек по координатам', () => {
      routePoints = [
        { coords: [55.751244, 37.618423], name: 'Точка А' },
        { coords: [55.753930, 37.621407], name: 'Точка Б' }
      ];
      
      removeRoutePoint(0);
      
      expect(routePoints.length).toBe(1);
      expect(routePoints[0].name).toBe('Точка Б');
    });
  });

  describe('setupResetButton', () => {
    it('обработка нажатия кнопки сброса', () => {
      setupResetButton();
      const resetBtn = document.getElementById('reset-route');
      resetBtn.click();
      expect(resetRoute).toHaveBeenCalled();
    });
  });

  describe('initInstructionsToggle', () => {
    it('обработка кнопки подробностей', () => {
      initInstructionsToggle();
      
      const toggleBtn = document.getElementById('toggle-instructions-btn');
      const instructionsContainer = document.getElementById('navigation-instructions');
      
      expect(toggleBtn.style.display).toBe('none');
      expect(instructionsContainer.style.display).toBe('none');
      
      toggleBtn.click();
      expect(instructionsContainer.style.display).toBe('block');
      expect(toggleBtn.textContent).toBe('Скрыть подробности');
      
      toggleBtn.click();
      expect(instructionsContainer.style.display).toBe('none');
      expect(toggleBtn.textContent).toBe('Показать подробности');
    });
  });
});