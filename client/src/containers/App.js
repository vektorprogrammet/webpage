import React, { Component } from 'react';
import { Switch, Route } from 'react-router-dom';

import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { fetchDepartments } from '../actions/department';
import { fetchSponsors } from '../actions/sponsor';

import Header from './Header';
import HomePage from './HomePage';
import AssistantPage from './AssistantPage';
import TeamPage from './TeamPage';
import AboutUsPage from './AboutUsPage';
import ContactPage from './ContactPage';
import LoginPage from './LoginPage';
import ReceiptPage from './ReceiptPage';
import DashboardPage from './DashboardPage';
import UserPage from './UserPage';

class App extends Component {
  componentDidMount() {
    this.props.fetchDepartments();
    this.props.fetchSponsors();
  }

  render() {
    return (
      <div>
        <Header/>
        <Switch>
          <Route exact path='/' component={HomePage}/>
          <Route exact path='/assistenter' component={AssistantPage}/>
          <Route exact path='/team' component={TeamPage}/>
          <Route exact path='/om-oss' component={AboutUsPage}/>
          <Route exact path='/kontakt' component={ContactPage}/>
          <Route exact path='/login' component={LoginPage}/>
          <Route exact path='/utlegg' component={ReceiptPage}/>
          <Route path='/bruker' component={UserPage}/>
          <Route path='/dashboard' component={DashboardPage}/>
        </Switch>
      </div>
    );
  }
}

const mapDispatchToProps = dispatch => bindActionCreators({
  fetchDepartments,
  fetchSponsors,
}, dispatch);

export default connect(null, mapDispatchToProps, null, {pure: false})(App);
