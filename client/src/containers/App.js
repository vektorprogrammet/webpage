import React, { Component } from 'react';
import { Switch, Route } from 'react-router-dom';
import Header from './Header';
import HomePage from './HomePage';
import AssistantPage from './AssistantPage';
import TeamPage from './TeamPage';

class App extends Component {
  render() {
    return (
        <div>
          <Header/>
          <Switch>
            <Route exact path='/' component={HomePage}/>
            <Route exact path='/assistenter' component={AssistantPage}/>
            <Route exact path='/team' component={TeamPage}/>
          </Switch>
        </div>
    );
  }
}

export default App;
