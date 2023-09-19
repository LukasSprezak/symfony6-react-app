import { createGlobalStyle } from 'styled-components';

const MainStyle = createGlobalStyle`
  
  *, *::before, *::after {
    box-sizing: border-box;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }
  
  html {
    font-size: 62.5%; 
  }
  
  body {
    padding-left: 150px;
    font-size: 1.6rem;
  }
`;

export default MainStyle;
